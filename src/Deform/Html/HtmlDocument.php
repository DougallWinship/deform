<?php

declare(strict_types=1);

namespace Deform\Html;

use Deform\Exception\DeformException;
use Deform\Exception\DeformHtmlException;

/**
 * basic DOMDocument helper
 *
 * please note this is a specific implementation for this library, for a more generic solution try one of the following:
 *   https://github.com/phpbench/dom
 *   https://github.com/PhpGt/Dom/wiki
 *   https://github.com/scotteh/php-dom-wrapper
 *
 * WARNING : Due to changes in libxml2 2.9.14+ (see https://github.com/php/doc-en/issues/2219), malformed HTML may no
 * longer produce parse errors.
 * Do not use this class to validate input correctness — it is only a parser.
 */
class HtmlDocument implements \Stringable
{
    public const HTML5_ALLOWED_TAGS = [
        'article', 'aside', 'details', 'figcaption', 'figure',
        'footer', 'header', 'main', 'mark', 'nav', 'section', 'summary',
        'time', 'datalist', 'canvas', 'svg', 'video', 'audio'
    ];

    /** @var \DOMDocument  */
    private \DOMDocument $domDocument;

    /** @var \DOMXPath|null  */
    private ?\DOMXPath $domXPath = null;

    /** @var array  */
    private array $errors = [];

    /** @var null|callable  */
    private static $cssToXpathConverter = null;

    /**
     * @var array|string[] whitelist of permitted tags
     */
    private static array $allowedTags = self::HTML5_ALLOWED_TAGS;

    /**
     * private to prevent instancing manually
     */
    private function __construct()
    {
        $this->domDocument = new \DOMDocument();
    }

    /**
     * Attempts to load an arbitrary snippet of HTML.
     * @param string|\Stringable $html
     * @param bool $allowAllTags
     * @return HtmlDocument
     */
    public static function load(string|\Stringable $html, bool $allowAllTags = false): HtmlDocument
    {
        $htmlDocument = new self();

        $internalErrors = libxml_use_internal_errors(true);

        $htmlString = is_string($html)
            ? $html
            : (string)$html;
        $htmlDocument->domDocument->loadHTML($htmlString, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $htmlDocument->processXmlErrors($allowAllTags);
        libxml_use_internal_errors($internalErrors);
        return $htmlDocument;
    }

    /**
     * @param bool $allowAllTags
     * @return void
     */
    private function processXmlErrors(bool $allowAllTags): void
    {
        $allErrors = libxml_get_errors();
        $allowedTags = self::$allowedTags;
        $this->errors = array_filter($allErrors, function ($error) use ($allowAllTags, $allowedTags) {
            $errorMessage = trim($error->message);
            if (preg_match('/Tag ([\w-]+) invalid\n?/', $errorMessage, $matches)) {
                if ($allowAllTags) {
                    return false;
                }
                return !in_array($matches[1], $allowedTags);
            } else {
                return true;
            }
        });
        libxml_clear_errors();
    }

    /**
     * @param array $allowedTags
     * @return void
     */
    public static function addAllowedTags(array $allowedTags): void
    {
        self::$allowedTags = array_unique(array_merge(self::$allowedTags, $allowedTags));
    }

    /**
     * @param array $allowedTags
     * @return void
     */
    public static function setAllowedTags(array $allowedTags): void
    {
        self::$allowedTags = $allowedTags;
    }

    /**
     * @return array|string[]
     */
    public static function getAllowedTags(): array
    {
        return self::$allowedTags;
    }

    public static function resetAllowedTags(): void
    {
        self::$allowedTags = self::HTML5_ALLOWED_TAGS;
    }

    /**
     * @return bool
     */
    public function hasErrors(): bool
    {
        return count($this->errors) > 0;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * generate an HtmlTag tree from the current document
     * @param bool $preserveWhitespace
     * @return HtmlTag
     * @throws DeformException
     */
    public function getHtmlRootTag(bool $preserveWhitespace = false): HtmlTag
    {
        return self::recurseDomElements($this->domDocument->documentElement, $preserveWhitespace);
    }

    /**
     * @param \DomElement $element
     * @param bool $preserveWhitespace
     * @return HtmlTag
     * @throws DeformException
     */
    protected static function recurseDomElements(\DomElement $element, bool $preserveWhitespace = false): HtmlTag
    {
        $tag = self::buildHtmlTagFromElement($element);

        /** @var \DOMNode $node */
        foreach ($element->childNodes as $node) {
            switch ($node->nodeType) {
                case XML_ELEMENT_NODE:
                    $childTag = self::recurseDomElements($node);
                    $tag->add($childTag);
                    break;

                case XML_TEXT_NODE:
                    if ($preserveWhitespace || strlen(trim($node->nodeValue)) > 0) {
                        $tag->add($node->nodeValue);
                    }
                    break;
            }
        }
        return $tag;
    }

    /**
     * @param \DomElement $element
     * @return HtmlTag
     * @throws DeformException
     */
    protected static function buildHtmlTagFromElement(\DomElement $element): HtmlTag
    {
        $attributes = [];
        if ($element->hasAttributes()) {
            /** @var \DOMAttr $attribute */
            foreach ($element->attributes as $attribute) {
                $attributes[$attribute->nodeName] = $attribute->nodeValue;
            }
        }
        return new HtmlTag($element->tagName, $attributes);
    }

    /**
     * @return \DOMXpath
     */
    protected function getDOMXpath(): \DOMXPath
    {
        if ($this->domXPath === null) {
            $this->domXPath = new \DOMXpath($this->domDocument);
        }
        return $this->domXPath;
    }

    /**
     * @param string $xpathQuery
     * @param callable $callback
     * @return self
     */
    public function selectXPath(string $xpathQuery, callable $callback): self
    {
        $domNodeList = $this->getDOMXpath()->query($xpathQuery);
        $this->applyCallback($domNodeList, $callback);
        return $this;
    }

    /**
     * @codeCoverageIgnore
     * @param string $cssSelector
     * @param callable $callback
     * @return self
     * @throws DeformException
     */
    public function selectCss(string $cssSelector, callable $callback): self
    {
        $xpathQuery = $this->convertCssSelectorToXpathQuery($cssSelector);
        $domNodeList = $this->getDOMXpath()->query($xpathQuery);
        if ($domNodeList === false) {
            throw new DeformHtmlException("Failed to retrieve CSS selector '{$cssSelector}'.");
        }
        $this->applyCallback($domNodeList, $callback);
        return $this;
    }

    /**
     * @param string $cssSelector
     * @return string
     * @throws DeformException
     */
    private function convertCssSelectorToXpathQuery(string $cssSelector): string
    {
        if (!is_callable(self::$cssToXpathConverter)) {
            throw new DeformHtmlException(
                "If you want to use css selectors then please specify a converter via setCssToXpathConverter"
            );
        }
        return call_user_func(self::$cssToXpathConverter, $cssSelector);
    }

    /**
     * @return bool
     */
    public static function canSelectCss(): bool
    {
        return is_callable(self::$cssToXpathConverter);
    }

    /**
     * @param callable $converter
     * @return void
     */
    public static function setCssToXpathConverter(callable $converter): void
    {
        self::$cssToXpathConverter = $converter;
    }

    /**
     * @param \DOMNodeList $domNodeList
     * @param callable $callback
     */
    protected function applyCallback(\DOMNodeList $domNodeList, callable $callback): void
    {
        foreach ($domNodeList as $domNode) {
            /** @var \DOMNode $domNode */
            ($callback)($domNode);
        }
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $html = $this->domDocument->saveHTML();
        return is_string($html) ? rtrim($html, PHP_EOL) : "";
    }
}
