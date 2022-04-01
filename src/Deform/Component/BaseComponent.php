<?php

declare(strict_types=1);

namespace Deform\Component;

use Deform\Html\Html;
use Deform\Html\HtmlTag;
use Deform\Html\IHtml;
use Deform\Util\IToString;
use Deform\Util\Strings;

abstract class BaseComponent implements IToString
{
    public const EXPECTED_DATA_FIELD = "expected_data";

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

    /** @var ?\Exception */
    private ?\Exception $toStringException = null;

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
     * @return $this
     */
    public function tooltip(string $tooltip): BaseComponent
    {
        $this->componentContainer->setTooltip($tooltip);
        return $this;
    }

    /**
     * set the component's label
     * @param string $label
     * @return $this
     * @throws \Exception
     */
    public function label(string $label): BaseComponent
    {
        $this->componentContainer->setLabel($label);
        return $this;
    }

    /**
     * set a hint for the component
     * @param $hint string
     * @return $this
     */
    public function hint(string $hint): BaseComponent
    {
        $this->componentContainer->setHint($hint);
        return $this;
    }

    /**
     * whether to try and guess the components label automatically
     * @param bool $autoLabel
     * @return $this
     */
    public function autolabel(bool $autoLabel): BaseComponent
    {
        $this->autoLabel = $autoLabel;
        return $this;
    }

    /**
     * add a control and optionally a decorator (an optional wrapper for the control)
     * @param HtmlTag $control
     * @param array|HtmlTag|null $controlTagDecorator
     * @return $this
     * @throws \Exception
     */
    public function addControl(HtmlTag $control, $controlTagDecorator = null): BaseComponent
    {
        $this->componentContainer->control->addControl($control, $controlTagDecorator);
        return $this;
    }

    /**
     * add an expected field (used for controls which do not submit data if they are unset such as checkboxes)
     * @param string $fieldName
     */
    public function addExpectedField(string $fieldName)
    {
        $this->componentContainer->addExpectedInput($fieldName, $this->getExpectedDataName());
    }

    /**
     * set an error on this component
     * @param $error string
     * @return $this
     */
    public function setError(string $error): BaseComponent
    {
        $this->componentContainer->setError($error);
        return $this;
    }

    /**
     * sets the components value
     * @param mixed $value
     * @return $this
     * @throws \Exception
     */
    public function setValue($value): self
    {
        $this->componentContainer->control->setValue($value);
        return $this;
    }

    /**
     * sets the component's form namespace
     * @param string $namespace
     * @return BaseComponent
     * @throws \Exception
     */
    public function setNamespace(string $namespace): BaseComponent
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
        return $this->componentContainer->generateHtmlTag($containerId, $this->attributes);
    }

    /**
     * convert this component to a string
     * @return string
     */
    public function __toString(): string
    {
        try {
            $tag = $this->getHtmlTag();
            return (string)$tag;
        } catch (\Exception $exc) {
            // https://wiki.php.net/rfc/tostring_exceptions
            $this->toStringException = $exc;
            return "";
        }
    }

    /**
     * @return ?\Exception
     */
    public function getLastToStringException()
    {
        return $this->toStringException;
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
    public function requiresMultiformEncoding()
    {
        return $this->requiresMultiformEncoding;
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
        ]);
    }

    /**
     * @param $attributes
     * @throws \Exception
     */
    public function setAttributes($attributes)
    {
        $this->componentContainer->setControlAttributes($attributes);
    }

    /**
     * @param $attributes
     * @throws \Exception
     */
    public function setContainerAttributes($attributes)
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
            $propertyValues[$propertyName] = $reflectionProperty->getValue($this);
        }
        return $propertyValues;
    }

    /**
     * @param array $properties
     * @throws \ReflectionException
     */
    public function setRegisteredPropertyValues(array $properties)
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
     * hydrate the component using its properties (those annotated as @persistAttribute) when it's being rebuilt
     * from an array definition
     */
    abstract public function hydrate();
}
