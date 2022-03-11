<?php

declare(strict_types=1);

namespace Deform\Html;

use Deform\Util\IToString;

class HtmlDocument implements IToString
{
    private \DOMDocument $domDocument;
    private ?\DOMXPath $domXPath = null;

    /**
     * prevent instancing manually
     */
    private function __construct()
    {
        $this->domDocument = new \DOMDocument();
    }

    /**
     * @param string|IHtml $html
     * @return HtmlDocument
     * @throws \Exception
     */
    public static function load($html): HtmlDocument
    {
        $htmlDocument = new self();
        set_error_handler(function (int $errno, string $errstr) {
            restore_error_handler();
            throw new \Exception($errstr . " (" . $errno . ")");
        });
        $htmlString = (string)$html;
        $htmlDocument->domDocument->loadHTML($htmlString, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        restore_error_handler();

        return $htmlDocument;
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
     * @return $this
     */
    public function selectXPath(string $xpathQuery, callable $callback): self
    {
        $domNodeList = $this->getDOMXpath()->query($xpathQuery);
        $this->applyCallback($domNodeList, $callback);
        return $this;
    }

    /**
     * @param string $cssSelector
     * @param callable $callback
     * @return $this
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
     * @param $cssSelector
     * @return string
     * @throws \Exception
     */
    protected function convertCssSelectorToXpathQuery($cssSelector): string
    {

        if (!class_exists('\bdk\CssXpath\CssXpath')) {
            // @codeCoverageIgnoreStart
            throw new \Exception("If you want to use css selectors then install https://github.com/bkdotcom/CssXpath");
            // @codeCoverageIgnoreEnd
        }
        return \bdk\CssXpath\CssXpath::cssToXpath($cssSelector);
    }

    /**
     * @param \DOMNodeList $domNodeList
     * @param callable $callback
     */
    protected function applyCallback(\DOMNodeList $domNodeList, callable $callback)
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
