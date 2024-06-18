<?php

declare(strict_types=1);

namespace Deform\Html;

/**
 * basic DOMDocument helper
 *
 * please note this is a specific implementation for this library, for a more generic solution try
 *   https://github.com/phpbench/dom
 * or
 *   https://github.com/scotteh/php-dom-wrapper
 */
class HtmlDocument implements \Stringable
{
    private \DOMDocument $domDocument;
    private ?\DOMXPath $domXPath = null;
    private array $errors = [];

    private static array $allowedTags = [
        'article', 'aside', 'details', 'figcaption', 'figure',
        'footer', 'header', 'main', 'mark', 'nav', 'section', 'summary',
        'time', 'datalist', 'canvas', 'svg', 'video', 'audio'
    ];

    /**
     * prevent instancing manually
     */
    private function __construct()
    {
        $this->domDocument = new \DOMDocument();
    }

    /**
     * @param string|\Stringable $html
     * @param bool $allowAllTags
     * @return HtmlDocument
     */
    public static function load(mixed $html, $allowAllTags=false): HtmlDocument
    {
        $htmlDocument = new self();;
        $internalErrors = libxml_use_internal_errors(true);
        $htmlString = is_string($html)
            ? $html
            : (string)$html;
        $htmlDocument->domDocument->loadHTML($htmlString, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $htmlDocument->processXmlErrors($allowAllTags);
        libxml_use_internal_errors($internalErrors);
        return $htmlDocument;
    }

    private function processXmlErrors($allowAllTags): void
    {
        $allErrors = libxml_get_errors();
        $allowedTags = self::$allowedTags;
        $this->errors = array_filter($allErrors, function ($error) use ($allowAllTags, $allowedTags) {
            if (preg_match('/Tag (\w+) invalid/', $error->message, $matches)) {
                if ($allowAllTags) {
                    return false;
                }
                return !in_array($matches[1], $allowedTags);
            }
            else {
                return true;
            }
        });
        libxml_clear_errors();
    }

    public static function addAllowedTags(array $allowedTags): void {
        self::$allowedTags = array_unique(array_merge(self::$allowedTags, $allowedTags));
    }

    public static function setAllowedTags(array $allowedTags): void {
        self::$allowedTags = $allowedTags;
    }

    public static function getAllowedTags(): array
    {
        return self::$allowedTags;
    }

    public function hasErrors(): bool {
        return count($this->errors) > 0;
    }

    public function getErrors(): array {
        return $this->errors;
    }


    /**
     * generate an HtmlTag tree from the current document
     * @param bool $preserveWhitespace
     * @return HtmlTag
     * @throws \Exception
     */
    public function getHtmlRootTag(bool $preserveWhitespace = false): HtmlTag
    {
        return self::recurseDomElements($this->domDocument->documentElement, $preserveWhitespace);
    }

    /**
     * @param \DomElement $element
     * @param bool $preserveWhitespace
     * @return HtmlTag
     * @throws \Exception
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
     * @throws \Exception
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
     * @throws \Exception
     */
    public function selectCss(string $cssSelector, callable $callback): self
    {
        $xpathQuery = $this->convertCssSelectorToXpathQuery($cssSelector);
        $domNodeList = $this->getDOMXpath()->query($xpathQuery);
        $this->applyCallback($domNodeList, $callback);
        return $this;
    }

    /**
     * @codeCoverageIgnore
     * @param $cssSelector
     * @return string
     * @throws \Exception
     */
    protected function convertCssSelectorToXpathQuery($cssSelector): string
    {
        if (!class_exists('\bdk\CssXpath\CssXpath')) {
            throw new \Exception("If you want to use css selectors then install https://github.com/bkdotcom/CssXpath");
        }
        return \bdk\CssXpath\CssXpath::cssToXpath($cssSelector);
    }

    /**
     * @codeCoverageIgnore
     * @return bool
     */
    public function canConvertCssSelectorToXpath(): bool
    {
        return class_exists('\bdk\CssXpath\CssXpath');
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
    public function __toString()
    {
        $html = $this->domDocument->saveHTML();
        return is_string($html) ? rtrim($html, PHP_EOL) : "";
    }
}
