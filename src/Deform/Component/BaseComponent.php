<?php

declare(strict_types=1);

namespace Deform\Component;

use Deform\Html\Html;
use Deform\Html\HtmlTag;
use Deform\Util\Strings;

/**
 * base component
 */
abstract class BaseComponent implements \Stringable
{
    use Shadow\BaseShadow;

    public const string EXPECTED_DATA_FIELD = "expected_data";

    public const string PART_PREFIX = "deform";

    /** @var bool whether to use auto labelling by default */
    public static bool $useAutoLabelling = true;

    /** @var \ReflectionProperty[] */
    private static array $registeredProperties = [];

    /** @var null|string */
    protected ?string $namespace = null;

    /** @var string field name for this value */
    protected string $fieldName;

    /** @var array */
    protected array $attributes;

    /** @var ComponentContainer */
    public ComponentContainer $componentContainer;

    /** @var null|string */
    private ?string $id = null;

    /** @var null|string */
    private ?string $name = null;

    /** @var null|string */
    private ?string $expectedDataName = null;

    /** @var bool */
    private bool $autoLabel;

    /** @var bool */
    protected bool $requiresMultiformEncoding = false;

    private array $wrapStack = [];

    /**
     * protected to prevent direct instantiation
     * @param string|null $namespace
     * @param string $fieldName
     * @param array $attributes
     * @throws \Exception
     * @see ComponentFactory use this instead
     */
    protected function __construct(?string $namespace, string $fieldName, array $attributes = [])
    {
        $this->namespace = $namespace;
        $this->fieldName = $fieldName;
        $this->attributes = $attributes;
        $this->componentContainer = new ComponentContainer(get_called_class());
        $this->autoLabel = self::$useAutoLabelling;
        $this->setup();
    }

    /**
     * perform initial component setup
     * @throws \Exception
     */
    abstract public function setup();

    /**
     * set a tooltip for the component
     * @param string $tooltip
     * @return self
    */
    public function tooltip(string $tooltip): static
    {
        $this->componentContainer->setTooltip($tooltip);
        return $this;
    }

    /**
     * set the component's label
     * @param string $label
     * @param bool $required
     * @return self
     * @throws \Exception
     */
    public function label(string $label, bool $required = false): static
    {
        $this->componentContainer->setLabel($label, $required);
        return $this;
    }

    /**
     * set a hint for the component
     * @param $hint string
     * @return self
     */
    public function hint(string $hint): static
    {
        $this->componentContainer->setHint($hint);
        return $this;
    }

    /**
     * whether to try and guess the components label automatically
     * @param bool $autoLabel
     * @return self
     */
    public function autolabel(bool $autoLabel): static
    {
        $this->autoLabel = $autoLabel;
        return $this;
    }

    /**
     * add a control and optionally a decorator (an optional wrapper for the control)
     * @param HtmlTag $control
     * @param array|HtmlTag|null $controlTagDecorator
     * @return self
     * @throws \Exception
     */
    public function addControl(HtmlTag $control, mixed $controlTagDecorator = null): static
    {
        $this->componentContainer->control->addControl($control, $controlTagDecorator);
        return $this;
    }

    /**
     * add an expected field (used for controls which do not submit data if they are unset such as checkboxes)
     * @param string $fieldName
     */
    public function addExpectedField(string $fieldName): void
    {
        $this->componentContainer->addExpectedInput($fieldName, $this->getExpectedDataName());
    }

    /**
     * set an error on this component
     * @param $error string
     * @return self
     */
    public function setError(string $error): static
    {
        $this->componentContainer->setError($error);
        return $this;
    }

    /**
     * sets the components value
     * @param mixed $value
     * @return static
     * @throws \Exception
     */
    public function setValue(mixed $value): static
    {
        if ($value === null) {
            $value = '';
        }
        $this->componentContainer->control->setValue($value);
        return $this;
    }

    /**
     * sets the component's form namespace
     * @param string $namespace
     * @return static
     * @throws \Exception
     */
    public function setNamespace(string $namespace): static
    {
        if ($this->namespace != $namespace) {
            $this->namespace = $namespace;
            $newId = self::generateId($namespace, $this->fieldName);
            $newName = self::generateName($namespace, $this->fieldName);
            $this->componentContainer->changeNamespacedAttributes($newId, $newName);
        }
        return $this;
    }

    /**
     * generate a tag containing the entire component
     * @return HtmlTag
     * @throws \Exception
     */
    public function getHtmlTag(): HtmlTag
    {
        if ($this->autoLabel && !$this->componentContainer->labelTag) {
            $this->componentContainer->labelTag = Html::label([])->add($this->fieldName);
        }
        $containerId = $this->namespace !== null
            ? $this->namespace . '-' . $this->fieldName . '-container'
            : $this->fieldName . '-container';
        $componentHtmlTag = $this->componentContainer->generateHtmlTag($containerId, $this->attributes);
        if (!$this->wrapStack) {
            return $componentHtmlTag;
        }
        $reversedWrapStack = array_reverse($this->wrapStack);
        $wrapTag = null;
        $lastWrapTag = null;
        $topWrapTag = null;
        foreach ($reversedWrapStack as $wrap) {
            $wrapTag = new HtmlTag($wrap[0], $wrap[1]);
            if (!$topWrapTag) {
                $topWrapTag = $wrapTag;
            }
            if ($lastWrapTag) {
                $lastWrapTag->add($wrapTag);
            }
            $lastWrapTag = $wrapTag;
        }
        $wrapTag->add($componentHtmlTag);
        return $topWrapTag;
    }

    /**
     * convert this component to a string
     * @return string
     * @throws \Exception
     */
    public function __toString(): string
    {
        return (string) $this->getHtmlTag();
    }


    /**
     * @return string
     */
    public function getName(): string
    {
        if (!$this->name) {
            $this->name = self::generateName($this->namespace, $this->fieldName);
        }
        return $this->name;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getId(): string
    {
        if (!$this->id) {
            $this->id = self::generateId($this->namespace, $this->fieldName);
        }
        return $this->id;
    }

    /**
     * @return string
     */
    public function getExpectedDataName(): string
    {
        if (!$this->expectedDataName) {
            $this->expectedDataName = self::generateExpectedDataName($this->namespace);
        }
        return $this->expectedDataName;
    }

    /**
     * @param string $basicSelector
     * @return array
     * @throws \Exception
     */
    public function findNodes(string $basicSelector): array
    {
        // need to ensure the components are all html so they can be searched
        $htmlTag = $this->getHtmlTag();
        return $htmlTag->findNodes($basicSelector);
    }

    /**
     * @return bool
     */
    public function requiresMultiformEncoding(): bool
    {
        return $this->requiresMultiformEncoding;
    }

    /**
     * set component attributes magically (you can indeed do some extremely dumb things with this)
     * @param string $name
     * @param array $arguments
     * @return BaseComponent
     */
    public function __call(string $name, array $arguments)
    {
        if (count($arguments) !== 1) {
            throw new \InvalidArgumentException(
                "Method " . get_class($this) . "::" . $name . " only accepts a single argument"
            );
        }
        $this->attributes[$name] = $arguments[0];
        return $this;
    }

    /**
     * @return array
     * @throws \ReflectionException
     */
    public function toArray(): array
    {
        return array_filter([
            'class' => get_class($this),
            'name' => $this->fieldName,
            'properties' => $this->getRegisteredPropertyValues(),
            'container' => $this->componentContainer->toArray(),
            'attributes' => $this->attributes,
            'wrapStack' => $this->wrapStack,
        ]);
    }

    /**
     * @param $attributes
     * @throws \Exception
     */
    public function setAttributes($attributes): void
    {
        $this->componentContainer->setControlAttributes($attributes);
    }

    /**
     * @param $attributes
     * @throws \Exception
     */
    public function setContainerAttributes($attributes): void
    {
        $this->componentContainer->setContainerAttributes($attributes);
    }

    /**
     * @return array
     * @throws \ReflectionException
     */
    private function getRegisteredPropertyValues(): array
    {
        $propertyValues = [];
        $reflectionProperties = self::getRegisteredReflectionProperties();
        foreach ($reflectionProperties as $propertyName => $reflectionProperty) {
            if ($value =  $reflectionProperty->getValue($this)) {
                $propertyValues[$propertyName] = $value;
            }
        }
        return $propertyValues;
    }

    /**
     * @param array $properties
     * @throws \ReflectionException
     */
    public function setRegisteredPropertyValues(array $properties): void
    {
        $reflectionProperties = self::getRegisteredReflectionProperties();
        foreach ($properties as $propertyName => $setPropertyValue) {
            if (!isset($reflectionProperties[$propertyName])) {
                throw new \Exception("There is no registered property '" . $propertyName . "'");
            }
            $reflectionProperties[$propertyName]->setValue($this, $setPropertyValue);
        }
    }

    /**
     * wrap this component with a tag
     * @param string $tag
     * @param array $attributes
     * @return $this
     */
    public function wrap(string $tag, array $attributes = []): static
    {
        $this->wrapStack[] = [$tag, $attributes];
        return $this;
    }

    /**
     * set the wrapped tag stack
     * @param array $wrapStack
     * @return void
     */
    public function setWrapStack(array $wrapStack): void
    {
        $this->wrapStack = $wrapStack;
    }

    /**
     * hydrate the component using its properties (those annotated as @persistAttribute) when it's being rebuilt
     * from an array definition
     * @throws \Exception
     */
    public function hydrate(): void
    {
    }

    // static methods

    /**
     * @param $namespace null|string
     * @param $field string
     * @return string
     */
    protected static function generateName(?string $namespace, string $field): string
    {
        return $namespace !== null
            ? $namespace . "[" . $field . "]"
            : $field;
    }

    /**
     * @param $namespace null|string
     * @param $field string
     * @return string
     * @throws \Exception
     */
    protected static function generateId(?string $namespace, string $field): string
    {
        $classWithoutNamespace = \Deform\Util\Strings::getClassWithoutNamespace(get_called_class());
        return strtolower($classWithoutNamespace) . ($namespace !== null ? '-' . $namespace : '') . '-' . $field;
    }

    /**
     * @param null|string $namespace
     * @return string
     */
    protected static function generateExpectedDataName(?string $namespace): string
    {
        return $namespace !== null
            ? $namespace . "[" . self::EXPECTED_DATA_FIELD . "][]"
            : self::EXPECTED_DATA_FIELD . "[]";
    }

    /**
     * @return array|\ReflectionProperty[]
     * @throws \ReflectionException
     */
    private static function getRegisteredReflectionProperties(): array
    {
        $thisClass = get_called_class();
        if (!isset(self::$registeredProperties[$thisClass])) {
            $reflectionSelf = new \ReflectionClass($thisClass);
            $properties = [];
            $comments = $reflectionSelf->getDocComment();
            if ($comments) {
                $commentLines = explode(PHP_EOL, $comments);
                array_walk($commentLines, function ($comment) use (&$properties, $reflectionSelf) {
                    $commentParts = explode(' ', Strings::trimInternal($comment));
                    if (count($commentParts) >= 2 && $commentParts[1] == '@persistAttribute') {
                        $propertyName = $commentParts[2];
                        if ($reflectionSelf->hasProperty($propertyName)) {
                            $property = $reflectionSelf->getProperty($propertyName);
                            $property->setAccessible(true);
                            $properties[$commentParts[2]] = $property;
                        } else {
                            throw new \Exception(
                                "Failed to find property $" . $propertyName .
                                " for class " . get_called_class() .
                                " for annotation : " . $comment
                            );
                        }
                    }
                });
            }
            self::$registeredProperties[$thisClass] = $properties;
        }
        return self::$registeredProperties[$thisClass];
    }

    /**
     * @param string $id
     * @param string $value
     * @return string
     */
    public static function getMultiControlId(string $id, string $value): string
    {
        return $id . '-' . str_replace(" ", "-", $value);
    }

    /**
     * obtain methods names which have the @templateMethod annotation
     * @return \ReflectionMethod[]
     * @throws \ReflectionException
     */
    public function getTemplateMethods(): array
    {
        $thisClass = get_called_class();
        $reflectionSelf = new \ReflectionClass($thisClass);
        $methods = $reflectionSelf->getMethods();
        $templateMethods = [];
        foreach ($methods as $method) {
            if ($method->class !== BaseComponent::class) {
                $docComment = $method->getDocComment();
                if ($docComment) {
                    $comments = explode(PHP_EOL, $method->getDocComment());
                    array_walk($comments, function ($comment) use ($method, &$templateMethods) {
                        $trimmed = Strings::trimInternal($comment);
                        if (strpos($trimmed, '* @templateMethod') === 0) {
                            $templateMethods[] = $method;
                        }
                    });
                }
            }
        }
        return $templateMethods;
    }

    /**
     * provides the tag decorated with 'part' attributes to permit shadow dom styling
     * @return string
     * @throws \Exception
     */
    public function getShadowTemplate(): string
    {
        $htmlTag = $this->getHtmlTag();
        $this->addPartAttributesRecursive($htmlTag);
        return (string)$htmlTag;
    }

    /**
     * @param HtmlTag $tag
     * @return void
     * @throws \Exception
     */
    public function addPartAttributesRecursive(HtmlTag $tag): void
    {
        if ($tag->has('class')) {
            $classes = array_filter(explode(" ", trim($tag->get('class'))));
            $prependedClasses = array_map(function ($class) {
                return self::PART_PREFIX . '-' . $class;
            }, $classes);
            $tag->set('part', implode(" ", $prependedClasses));
        } else {
            if ($tag->has('type')) {
                $tag->set('part', self::PART_PREFIX . "-" . $tag->getTagType() . ' ' .
                    self::PART_PREFIX . "-" . $tag->getTagType() . "-" . $tag->get('type'));
            } else {
                $tag->set('part', self::PART_PREFIX . "-" . $tag->getTagType());
            }
        }
        if ($tag->hasChildren()) {
            $children = $tag->getChildren();
            foreach ($children as $child) {
                if ($child instanceof HtmlTag) {
                    $this->addPartAttributesRecursive($child);
                }
            }
        }
    }

    public static function getGitVersions(): array
    {
        static $versions = null;
        if ($versions !== null) {
            return $versions;
        }
        $full = trim(shell_exec('git describe --tags --always 2>/dev/null')) ?: '?';
        // Extract the "short" version by removing commit count and hash
        $short = preg_replace('/^v?([0-9]+\.[0-9]+\.[0-9]+).*$/', '$1', $full);
        $versions = [$short,$full];
        return $versions;
    }
}
