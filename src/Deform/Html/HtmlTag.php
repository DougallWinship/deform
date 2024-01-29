<?php

declare(strict_types=1);

namespace Deform\Html;

/**
 * represents empty (or self-closing) HTML tag incapable of containing child elements.
 *
 * non-authoritative list of potentially useful tag attributes to facilitate auto-completion:
 * @method HtmlTag value(string $value)
 * @method HtmlTag checked(bool $value)
 * @method HtmlTag selected(bool $value)
 * @method HtmlTag title(string $title)
 * @method HtmlTag name(string $name)
 * @method HtmlTag id(string $id)
 * @method HtmlTag for(string $for)
 * @method HtmlTag type(string $type)
 * @method HtmlTag autocomplete(string $autocomplete)
 * @method HtmlTag onsubmit(string $onsubmit)
 * @method HtmlTag onclick(string $onclick)
 * @method HtmlTag onchange(string $onchange)
 * @method HtmlTag oninput(string $oninput)
 * @method HtmlTag style(string $style)
 * @method HtmlTag force_style(string $style)
 * @method HtmlTag disabled(string $value)
 * @method HtmlTag class(string $value)
 * @method HtmlTag label(string $value)
 */
class HtmlTag implements IHtml
{
    /** @var string name of this tag type */
    protected string $tagName;

    /** @var array set of attributes to apply to this tag */
    protected array $attributes = [];

    /** @var bool whether this tag can contain children */
    protected bool $isSelfClosing;

    /** @var HtmlTag[] */
    private array $childTags = [];

    /**
     * @param string $tagName
     * @param array $attributes optionally specify initial attributes in an associative array
     *
     * @throws \Exception
     */
    public function __construct(string $tagName, array $attributes = [])
    {
        if (!Html::isRegisteredTag($tagName)) {
            throw new \Exception("Unregistered html tag '" . $tagName . "'");
        }
        $this->tagName = $tagName;
        $this->attributes = $attributes;
        $this->isSelfClosing = Html::isSelfClosedTag($tagName);
    }

    /**
     * add a child or children
     * @param string|string[]|HtmlTag|HtmlTag[] $childNodes
     * @throws \Exception
     */
    public function add($childNodes): HtmlTag
    {
        $this->disallowSelfClosingCheck();
        if (is_array($childNodes)) {
            foreach ($childNodes as $node) {
                $this->childTags[] = $node;
            }
        } else {
            $this->childTags[] = $childNodes;
        }
        return $this;
    }

    /**
     * prepend a child
     * @param string|HtmlTag $childNode
     * @throws \Exception
     */
    public function prepend($childNode): HtmlTag
    {
        $this->disallowSelfClosingCheck();
        array_unshift($this->childTags, $childNode);
        return $this;
    }

    /**
     * clears any children
     * @return $this
     * @throws \Exception
     */
    public function clear(): HtmlTag
    {
        $this->disallowSelfClosingCheck();
        $this->childTags = [];
        return $this;
    }

    /**
     * replace any children with a new child
     * @param IHtml|string $htmlTag
     * @return $this
     * @throws \Exception
     */
    public function reset($htmlTag): HtmlTag
    {
        $this->disallowSelfClosingCheck();
        $this->childTags = [];
        $this->add($htmlTag);
        return $this;
    }

    /**
     * whether this is a self-closing tag or not
     * @return bool
     */
    public function isSelfClosing(): bool
    {
        return $this->isSelfClosing;
    }

    /**
     * get any children
     * @return HtmlTag[]
     * @throws \Exception
     */
    public function getChildren(): array
    {
        $this->disallowSelfClosingCheck();
        return $this->childTags;
    }

    /**
     * either false if no children or how many there are
     * @return false|int
     */
    public function hasChildren()
    {
        if ($this->isSelfClosing || count($this->childTags) === 0) {
            return false;
        }
        return count($this->childTags);
    }

    /**
     * explicit attribute setter (avoiding the magic!)
     * @param string $name
     * @param string|array $arguments
     * @return HtmlTag
     * @throws \Exception
     */
    public function set(string $name, $arguments): HtmlTag
    {
        if (is_array($arguments)) {
            $arguments_string = self::implodeAttributeValues($name, $arguments);
            $this->mergeAttributes([$name => $arguments_string]);
        } elseif (is_scalar($arguments)) {
            $this->mergeAttributes([$name => $arguments]);
        }
        return $this;
    }

    /**
     * set an attribute if it already exists
     * @param string $name
     * @param string|array $arguments
     * @return $this
     * @throws \Exception
     */
    public function setIfExists(string $name, $arguments): HtmlTag
    {
        if (array_key_exists($name, $this->attributes)) {
            $this->set($name, $arguments);
        }
        return $this;
    }

    /**
     * set many attributes in one go (overwriting any that already exist)
     * @param array $attributes
     * @return HtmlTag
     * @throws \Exception
     */
    public function setMany(array $attributes): HtmlTag
    {
        foreach ($attributes as $name => $arguments) {
            $this->set($name, $arguments);
        }
        return $this;
    }

    /**
     * set an attribute if it doesn't already exist
     * @param string $name
     * @param string|array $arguments
     * @return HtmlTag
     * @throws \Exception
     */
    public function setIfEmpty(string $name, $arguments): HtmlTag
    {
        if (!isset($this->attributes[$name])) {
            $this->set($name, $arguments);
        }
        return $this;
    }

    /**
     * unset an attribute
     * @param string $name
     * @return HtmlTag
     */
    public function unset(string $name): HtmlTag
    {
        if (isset($this->attributes[$name])) {
            unset($this->attributes[$name]);
        }
        return $this;
    }

    /**
     * whether an attribute is set
     * breaks chaining!!
     * @param $name
     * @return bool
     */
    public function has($name): bool
    {
        return isset($this->attributes[$name]);
    }

    /**
     * returns an attribute value or null if it doesn't exist
     * breaks chaining!!
     * @param $name
     * @return string|null
     */
    public function get($name): ?string
    {
        return $this->attributes[$name] ?? null;
    }

    /**
     * not really sure about this one ... it's kinda convenient, but also can result in bad function calls
     * inadvertently leaking into the html
     *
     * set tag attributes. for example:
     *   $tag->value("wibble")->foo("bar")
     * generates the attributes:
     *   'value="wibble" foo="bar"'
     *
     * @param string $name
     * @param array $arguments
     * @return HtmlTag
     * @throws \Exception
     */
    public function __call(string $name, array $arguments)
    {
        return $this->set($name, $arguments);
    }

    /**
     * the html tag type
     * breaks chaining!!
     * @return string
     */
    public function getTagType(): string
    {
        return $this->tagName;
    }

    /**
     * set a single css rule without affecting any others in the style attribute
     * @param string $setRule
     * @param string $setValue
     * @return HtmlTag
     */
    public function css(string $setRule, string $setValue): HtmlTag
    {
        $cssParts = isset($this->attributes["style"]) ? explode(";", $this->attributes["style"]) : [];
        $rebuildStyle = [];
        foreach ($cssParts as $cssPart) {
            list($rule, $value) = explode(":", $cssPart);
            if ($rule != $setRule) {
                $rebuildStyle[] = $rule . ":" . $value;
            }
        }
        $rebuildStyle[] = $setRule . ":" . $setValue;
        $this->attributes["style"] = implode(";", $rebuildStyle);
        return $this;
    }

    /**
     * manually merge an array of attributes into the currently set ones
     * @param array $attributes
     * @return HtmlTag
     */
    public function mergeAttributes(array $attributes): HtmlTag
    {
        $this->attributes = array_merge($this->attributes, $attributes);
        return $this;
    }

    /**
     * recursively (via string coercion) generates the html string for this tag and all it's children
     * @return string
     * @throws \Exception note that generating an exception inside __toString() does bad things!
     */
    public function __toString()
    {
        $html = "<" . $this->tagName . self::attributesString($this->attributes) . ">";
        if (!$this->isSelfClosing) {
            foreach ($this->childTags as $childTag) {
                $html .= $childTag; // cast childTag to a string
            }
            $html .= "</" . $this->tagName . ">";
        }
        return $html;
    }

    /**
     * helper method for composing html attributes from an array
     * @param array $attributes associative array of attribute keys and values
     * @return string
     * @throws \Exception
     */
    public static function attributesString(array $attributes): string
    {
        if (!count($attributes)) {
            return "";
        }
        $buildAttributes = [];
        foreach ($attributes as $key => $value) {
            if (!is_string($key)) {
                continue;// discard any numeric keys!
            } elseif (!is_array($value) && !is_scalar($value)) {
                continue;// discard any values which are not an array or scalar!
            }
            if (is_bool($value)) {
                if (!$value) {
                    // strictly speaking if 'selected' or 'checked' is present then it should be honoured in html land
                    // however let's make an exception for the particular case where it's been set to bool false
                    continue;
                }
                $buildAttributes[$key] = $key;
            } else {
                if (is_array($value)) {
                    $value = self::implodeAttributeValues($key, $value);
                }
                $buildAttribute = strtolower($key);// attribute names are case-insensitive
                $buildAttribute .= "='" . htmlspecialchars((string)$value) . "'";
                $buildAttributes[$key] = $buildAttribute;
            }
        }

        return " " . implode(" ", $buildAttributes);
    }

    /**
     * different behaviour is useful for different attribute types
     * @param string $key
     * @param array $values
     * @return string
     * @throws \Exception
     */
    public static function implodeAttributeValues(string $key, array $values): string
    {
        if (substr($key, 0, 2) == 'on') {
            // assume it's onclick, onsubmit, onhover, etc ... it's up to the user to get these right!
            return implode(";", $values);
        } elseif ($key == 'style') {
            return implode(";", $values);
        } elseif ($key == 'class') {
            return implode(" ", $values);
        } else {
            // for any other attribute types just try to use the last one found...
            $lastElement = end($values);
            if (!is_scalar($lastElement)) {
                throw new \Exception(
                    "Unexpected non string attribute type for key='" . $key . "' = " . print_r($lastElement)
                );
            }
            return strval($lastElement);
        }
    }

    /**
     * very basic manipulation of the HtmlTag tree, to do anything more complex convert it to DOMDocument instead
     * the selector is *extremely* primitive & only supports (i.e. no descendants etc.)
     *  "tag" - a tag type
     *  "#id" - an id
     *  ".class" - a class
     * @param string $basicSelector a very basic selector only supports tag, .class & #id currently
     * @param callable $callback a callback to apply to the selected nodes
     * @return HtmlTag
     */
    public function deform(string $basicSelector, callable $callback): HtmlTag
    {
        $nodes = $this->findNodes($basicSelector);
        foreach ($nodes as $node) {
            $callback($node);
        }
        return $this;
    }

    /**
     * recursively searches the node and it's children for a set of nodes using the basic selector
     * @param string $basicSelector
     * @return array
     */
    public function findNodes(string $basicSelector): array
    {
        $nodes = [];
        if ($basicSelector === $this->tagName) {
            $nodes[] = $this;
        } elseif (isset($this->attributes['id']) && $basicSelector == '#' . $this->attributes['id']) {
            $nodes[] = $this;
        } elseif (isset($this->attributes['class'])) {
            $classes = explode(' ', $this->attributes['class']);
            foreach ($classes as $checkClass) {
                if (isset($this->attributes['class']) && $basicSelector == '.' . $checkClass) {
                    $nodes[] = $this;
                }
            }
        }
        foreach ($this->childTags as $childTag) {
            if ($childTag instanceof ISelectableNodes) {
                $childNodes = $childTag->findNodes($basicSelector);
                $nodes = array_merge($nodes, $childNodes);
            }
        }
        return $nodes;
    }

    /**
     * gets a corresponding DOMElement for this node for use with the specified domDocument
     * @param \DOMDocument $domDocument
     * @return \DOMElement|false
     */
    public function getDomNode(\DOMDocument $domDocument): \DOMNode
    {
        $node = $domDocument->createElement($this->tagName);
        foreach ($this->attributes as $key => $value) {
            $node->setAttribute($key, $value);
        }
        foreach ($this->childTags as $child) {
            if ($child instanceof IToDomNode) {
                $node->appendChild($child->getDomNode($domDocument));
            } elseif (is_string($child)) {
                $node->appendChild($domDocument->createTextNode($child));
            }
        }
        return $node;
    }

    /**
     * internal check for invalid operations on self-closing tags
     * @throws \Exception
     */
    private function disallowSelfClosingCheck()
    {
        if ($this->isSelfClosing) {
            $callingMethod = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
            $method = $callingMethod[1]['function'];
            throw new \Exception("You can't call '" . $method . "' on a '" . $this->tagName . "' tag!");
        }
    }
}
