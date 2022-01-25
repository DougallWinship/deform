<?php

declare(strict_types=1);

namespace Deform\Form\Model;

use Deform\Component\BaseComponent;
use Deform\Component\ComponentFactory;
use Deform\Component\File;
use Deform\Component\Image;
use Deform\Html\Html;
use Deform\Html\HtmlTag;
use Deform\Html\IHtml;
use Deform\Util\Arrays;

/**
 * @method \Deform\Component\Button addButton(string $field, array $options=[])
 * @method \Deform\Component\Checkbox addCheckbox(string $field, array $options=[])
 * @method \Deform\Component\CheckboxMulti addCheckboxMulti(string $field, array $options=[])
 * @method \Deform\Component\Currency addCurrency(string $field, array $options=[])
 * @method \Deform\Component\Date addDate(string $field, array $options=[])
 * @method \Deform\Component\DateTime addDateTime(string $field, array $options=[])
 * @method \Deform\Component\Display addDisplay(string $field, array $options=[])
 * @method \Deform\Component\Email addEmail(string $field, array $options=[])
 * @method \Deform\Component\File addFile(string $field, array $options=[])
 * @method \Deform\Component\Hidden addHidden(string $field, array $options=[])
 * @method \Deform\Component\Image addImage(string $field, array $options=[])
 * @method \Deform\Component\InputButton addInputButton(string $field, array $options=[])
 * @method \Deform\Component\MultipleEmail addMultipleEmail(string $field, array $options=[])
 * @method \Deform\Component\MultipleFile addMultipleFile(string $field, array $options=[])
 * @method \Deform\Component\Password addPassword(string $field, array $options=[])
 * @method \Deform\Component\RadioButtonSet addRadioButtonSet(string $field, array $options=[])
 * @method \Deform\Component\Select addSelect(string $field, array $options=[])
 * @method \Deform\Component\SelectMulti addSelectMulti(string $field, array $options=[])
 * @method \Deform\Component\Submit addSubmit(string $field, array $options=[])
 * @method \Deform\Component\Text addText(string $field, array $options=[])
 * @method \Deform\Component\TextArea addTextArea(string $field, array $options=[])
 */
class FormModel
{
    public const METHOD_GET = 'get';
    public const METHOD_POST = 'post';
    public const HTML_KEY = "HTML:";

    public const ENCTYPE_MULTIPART_URL_ENCODED = "application/x-www-form-urlencoded";
    public const ENCTYPE_MULTIPART_FORM_DATA = "multipart/form-data";

    /** @var int */
    private int $htmlCounter = 1;

    /** @var string */
    private string $namespace;

    /** @var array  */
    private array $sections = [];

    /** @var array */
    private array $formData;

    /** @var string */
    private string $formAction;

    /** @var string */
    private string $formMethod;

    /** @var string */
    private string $formEnctype = self::ENCTYPE_MULTIPART_URL_ENCODED;

    /**
     * @param string $namespace
     * @param string $formMethod
     * @param string $formAction
     */
    public function __construct(
        string $namespace,
        string $formMethod = self::METHOD_POST,
        string $formAction = ''
    ) {
        $this->namespace = $namespace;
        $this->formMethod = $formMethod;
        $this->formAction = $formAction;
    }

    /**
     * @param $name
     * @param $arguments
     * @return |void
     * @throws \Exception
     */
    public function __call($name, $arguments)
    {
        if (
            substr($name, 0, 3) === 'add'
            && strlen($name) > 3
            && count($arguments) > 0
        ) {
            $componentName = substr($name, 3);
            if (!ComponentFactory::isRegisteredComponent($componentName)) {
                throw new \Exception(
                    "There is no component named '" . $componentName . " registered in ComponentFactory"
                );
            }
            if (!is_string($field = $arguments[0])) {
                throw new \Exception("Unexpected type " . gettype($arguments[0]) . " for 'field' argument");
            }
            $options = $arguments[1] ?? [];
            if (!is_array($options)) {
                throw new \Exception("Unexpected type " . gettype($arguments[1]) . " for 'options' argument");
            }
            $component = ComponentFactory::build(
                $componentName,
                $this->namespace,
                $field,
                $options
            );
            $this->sections[$arguments[0]] = $component;
            return $component;
        }
        throw new \BadMethodCallException("Call to undefined method " . __CLASS__ . "::" . $name . "()");
    }

    /**
     * @param string|IHtml $html
     * @throws \Exception
     */
    public function addHtml($html)
    {
        if ($html instanceof IHtml) {
            $html = (string)$html;
        }
        if (!is_string($html)) {
            throw new \Exception("Add HTML either as a string or as an HtmlTag");
        }
        $this->sections[self::HTML_KEY . ($this->htmlCounter++)] = $html;
    }

    /**
     * @return HtmlTag
     * @throws \Exception
     */
    public function getFormHtml(): HtmlTag
    {
        $formAttributes = [
            'method' => $this->formMethod,
            'action' => $this->formAction
        ];
        foreach ($this->sections as $section) {
            if (($section instanceof BaseComponent) && ($section->requiresMultiformEncoding())) {
                $formAttributes['enctype'] = self::ENCTYPE_MULTIPART_FORM_DATA;
            }
        }
        $formHtml = Html::form($formAttributes);

        foreach ($this->sections as $key => $section) {
            if ($section instanceof BaseComponent) {
                //$section->setNameForNamespace($this->namespace);
                $sectionTag = $section->getHtmlTag();
                $formHtml->add($sectionTag);
            } elseif (is_string($section) || $section instanceof IHtml) {
                $formHtml->add($section);
            } else {
                throw new \Exception("Unexpected section type " . gettype($section) . " for section '" . $key . "'");
            }
        }
        return $formHtml;
    }

    /**
     * @param null|array $data
     * @return bool
     * @throws \Exception
     */
    public function process(array $data = [])
    {
        if (!self::isSubmitted($this->formMethod)) {
            return false;
        }

        if ($this->populateFormData($data)) {
            if ($this->validateFormData()) {
                $this->processFormData();
            }
            // if the form didn't validate it was still processed!
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param null|array $data
     * @return bool
     * @throws \Exception
     */
    protected function populateFormData(array $data = null)
    {
        if ($data === null) {
            $rawData = self::getFormData($this->formMethod);
            if (!isset($rawData[$this->namespace])) {
                return false;
            }
            $this->formData = $rawData[$this->namespace];
        } else {
            $this->formData = $data;
        }
        return true;
    }

    /**
     * @return bool
     */
    protected function validateFormData()
    {
        return true;
    }

    protected function processFormData()
    {
        echo "<pre>" . print_r($this->formData, true) . "</pre>";
    }

    /**
     * @param string $method
     * @return array
     * @throws \Exception
     */
    protected static function getFormData(string $method)
    {
        switch (strtolower($method)) {
            case self::METHOD_GET:
                return $_GET;
            case self::METHOD_POST:
                return $_POST;
            default:
                throw new \Exception("Unrecognised form method '" . $method . "'");
        }
    }

    protected static function isSubmitted($method)
    {
        switch (strtolower($method)) {
            case self::METHOD_GET:
                return $_GET;
            case self::METHOD_POST:
                return  $_SERVER['REQUEST_METHOD'] == 'POST';
            default:
                throw new \Exception("Unrecognised form method '" . $method . "'");
        }
    }

    public function getFieldComponent($field)
    {
        return $this->sections[$field];
    }

    public function toArray(): array
    {
        $formDefinition = [
            'tag' => 'form',
            'namespace' => $this->namespace,
            'action' => $this->formAction,
            'method' => $this->formMethod,
        ];
        $sectionsArray = [];
        foreach ($this->sections as $name => $section) {
            if (strpos($name, self::HTML_KEY) === 0) {
                $sectionsArray[] = [
                    'html' => (string)$section
                ];
            } elseif ($section instanceof BaseComponent) {
                $sectionsArray[] = $section->toArray();
            }
        }
        $formDefinition['sections'] = $sectionsArray;
        return $formDefinition;
    }

    public static function buildForm(array $definition)
    {
        try {
            $definitionParts = Arrays::extractKeys($definition, [
                'tag',
                'namespace',
                'action',
                'method',
                'sections'
            ], true);
        } catch (\Exception $exc) {
            throw new \InvalidArgumentException(
                "Definition must contain the keys 'tag','namespace','action','method','sections'"
            );
        }
        if ($definitionParts['tag'] !== 'form') {
            throw new \InvalidArgumentException("Definition only supports the tag 'form'");
        }
        $formModel = new self(
            $definitionParts['namespace'],
            $definitionParts['method'],
            $definitionParts['action']
        );
        foreach ($definitionParts['sections'] as $section) {
            if (isset($section['class'])) {
                try {
                    $class = $section['class'];
                    $name = $section['name'];
                    $properties = $section['properties'] ?? [];
                    $attributes = $section['attributes'] ?? [];
                    $container = $section['container'] ?? null;
                    unset($section['class'], $section['name']);
                    $component = ComponentFactory::build($class, $formModel->namespace, $name, $attributes);
                    $component->setAttributes($attributes);
                    $component->setRegisteredPropertyValues($properties);
                    if ($container) {
                        $component->setContainerAttributes($container);
                    }
                    $component->hydrate();
                    $formModel->sections[$name] = $component;
                } catch (\Exception $exc) {
                    throw new \Exception(
                        "Invalid component definition 'class','namespace','name' & 'id' are required :"
                        . print_r($attributes, true)
                    );
                }
            } elseif (isset($section['html'])) {
                $formModel->addHtml($section['html']);
            }
        }
        return $formModel;
    }
}
