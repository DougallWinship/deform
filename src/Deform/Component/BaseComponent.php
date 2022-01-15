<?php

declare(strict_types=1);

namespace Deform\Component;

use Deform\Html\Html;
use Deform\Html\IHtml;

abstract class BaseComponent implements IHtml
{
    public const EXPECTED_DATA_FIELD = "expected_data";

    /** @var bool whether to use auto labelling by default */
    public static bool $useAutoLabelling = true;

    /** @var string */
    protected string $namespace;

    /** @var string field name for this value */
    protected string $fieldName;

    /** @var array  */
    protected array $attributes;

    /** @var ComponentContainer */
    public ComponentContainer $componentContainer;

    /** @var string */
    private $id;

    /** @var string */
    private $name;

    /** @var string */
    private $expectedDataName;

    /** @var bool */
    private $autoLabel;

    /**
     * protected to prevent direct instantiation
     * @see ComponentFactory use this instead
     * @param string $namespace
     * @param string $fieldName
     * @param array $attributes
     * @throws \Exception
     */
    protected function __construct(string $namespace, string $fieldName, array $attributes = [])
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
        $this->componentContainer->tooltip = $tooltip;
        return $this;
    }

    /**
     * @param string $label
     * @return $this
     * @throws \Exception
     */
    public function label(string $label): BaseComponent
    {
        $this->componentContainer->labelTag = Html::label(['style' => 'margin-bottom:0'])->add($label);
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
     * @param IHtml|array $control
     * @return $this
     */
    public function control($control): BaseComponent
    {
        $this->componentContainer->controlTag = $control;
        return $this;
    }

    /**
     * @param IHtml|array $control
     * @param bool $force
     * @return $this
     * @throws \Exception
     */
    public function addControl($control, bool $force = false): BaseComponent
    {
        if ($force) {
            if (!is_array($this->componentContainer->controlTag)) {
                // resets as an array
                $this->componentContainer->controlTag = [];
            }
        } elseif (!is_array($this->componentContainer->controlTag) && $this->componentContainer->controlTag) {
            throw new \Exception(
                "There is already a single control specified, "
                . "if you want to replace it and add multiple control items use the force flag"
            );
        }
        $this->componentContainer->controlTag[] = $control;
        return $this;
    }

    /**
     * @param $hint string
     * @return $this
     */
    public function hint(string $hint): BaseComponent
    {
        $this->componentContainer->hintTag = $hint;
        return $this;
    }

    /**
     * @param $error string
     * @return $this
     */
    public function error(string $error): BaseComponent
    {
        $this->componentContainer->errorTag = $error;
        return $this;
    }

    public function getDomNode(\DOMDocument $document)
    {
        return Html::getDOMDocument($this->getHtmlTag());
    }

    public function getHtmlTag(): IHtml
    {
        if ($this->autoLabel && !$this->componentContainer->labelTag) {
            $this->componentContainer->labelTag = Html::label([])->add($this->fieldName);
        }
        return $this->componentContainer->getHtmlTag($this->namespace . '-' . $this->fieldName . '-container');
    }

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
     */
    public function findNodes(string $basicSelector): array
    {
        // need to ensure the components are all html so they can be searched
        $htmlTag = $this->getHtmlTag();
        return $htmlTag->findNodes($basicSelector);
    }

    // static methods

    /**
     * @param $namespace string
     * @param $field string
     * @return string
     */
    protected static function generateName(string $namespace, string $field): string
    {
        return $namespace . "[" . $field . "]";
    }

    /**
     * @param $namespace string
     * @param $field string
     * @return string
     * @throws \Exception
     */
    protected static function generateId(string $namespace, string $field): string
    {
        $classWithoutNamespace = \Deform\Util\Strings::getClassWithoutNamespace(get_called_class());
        return strtolower($classWithoutNamespace) . '-' . $namespace . '-' . $field;
    }

    /**
     * @param $namespace string
     * @return string
     */
    protected static function generateExpectedDataName(string $namespace): string
    {
        return $namespace . "[" . self::EXPECTED_DATA_FIELD . "][]";
    }

    public function toArray(): array
    {
        return [
            'class' => get_class($this),
            'namespace' => $this->namespace,
            'name' => $this->name,
            'id' => $this->id,
            'autolabel' => $this->autoLabel,
            'container' => $this->componentContainer !== null
        ];
    }
}
