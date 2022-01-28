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

    /** @var array  */
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

    /**
     * protected to prevent direct instantiation
     * @see ComponentFactory use this instead
     * @param string|null $namespace
     * @param string $fieldName
     * @param array $attributes
     * @throws \Exception
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

    abstract public function setup();

    /**
     * @param string $tooltip
     * @return $this
     */
    public function tooltip(string $tooltip): BaseComponent
    {
        $this->componentContainer->setTooltip($tooltip);
        return $this;
    }

    /**
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
     * @param $hint string
     * @return $this
     */
    public function hint(string $hint): BaseComponent
    {
        $this->componentContainer->setHint($hint);
        return $this;
    }

    /**
     * @param bool $autoLabel
     * @return $this
     */
    public function autolabel(bool $autoLabel): BaseComponent
    {
        $this->autoLabel = $autoLabel;
        return $this;
    }

    /**
     * @param HtmlTag $control
     * @param array|IHtml|string|null $controlTagDecorator
     * @return $this
     * @throws \Exception
     */
    public function addControl(HtmlTag $control, $controlTagDecorator = null): BaseComponent
    {
        $this->componentContainer->control->addControl($control, $controlTagDecorator);
        return $this;
    }

    public function addExpectedField(string $fieldName)
    {
        $this->componentContainer->addExpectedInput($fieldName, $this->getExpectedDataName());
    }

    /**
     * @param $error string
     * @return $this
     */
    public function error(string $error): BaseComponent
    {
        $this->componentContainer->setError($error);
        return $this;
    }

    /**
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
            $this->componentContainer->changeNamespaceAttributes($newId, $newName);
        }
        return $this;
    }

    /**
     * @param \DOMDocument $document
     * @return mixed
     * @throws \Exception
     */
    public function getDomNode(\DOMDocument $document)
    {
        return Html::getDOMDocument($this->getHtmlTag());
    }

    /**
     * @return IHtml
     * @throws \Exception
     */
    public function getHtmlTag(): IHtml
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
     * @return string
     */
    public function __toString(): string
    {
        try {
            return (string)$this->getHtmlTag();
        } catch (\Exception $exc) {
            die("<pre>" . htmlspecialchars(print_r($exc, true)) . "</pre>");
        }
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
            ?  $namespace . "[" . self::EXPECTED_DATA_FIELD . "][]"
            : self::EXPECTED_DATA_FIELD . "[]";
    }

    /**
     * you can indeed do dumb things with this
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
     * hydrate the component using its properties
     */
    public function hydrate()
    {
        // override to rebuild component dependencies when the component is being rebuilt from an array
        // for example Select has $options values but hasn't yet actually build their tags
    }
}
