<?php

declare(strict_types=1);

namespace Deform\Component;

use Deform\Exception\DeformComponentException;
use Deform\Exception\DeformException;
use Deform\Html\Html as Html;
use Deform\Html\HtmlTag;
use Deform\Html\IHtml;

/**
 * Defines a standard wrapper for components.
 */
class ComponentContainer
{
    /** @var bool */
    public bool $controlOnly = false;

    /** @var bool|string */
    public string|bool $tooltip = false;

    /** @var string */
    public string $containerType;

    /** @var array|string[] */
    public array $controlContainerAttributes = ['class' => 'control-container'];

    /** @var null|ComponentControls */
    public ?ComponentControls $control;

    /** @var array|string[]  */
    public array $labelContainerAttributes = ['class' => 'label-container'];
    /** @var null|string  */
    public ?string $label = null;
    /** @var bool|IHtml */
    public bool|IHtml $labelTag = false;
    /** @var bool  */
    public bool $disableLabel = false;

    /** @var array|string[] */
    public array $hintContainerAttributes = ['class' => 'hint-container'];
    /** @var null|string */
    public ?string $hint = null;
    /** @var bool|IHtml */
    public $hintTag = false;
    /** @var bool */
    public bool $disableHint = false;

    /** @var array|string[]  */
    public array $errorContainerAttributes = ['class' => 'error-container'];
    /** @var bool|IHtml */
    public $errorTag = false;
    /** @var bool  */
    public bool $disableError = false;
    /** @var HtmlTag|null */
    private ?HtmlTag $expectedInput = null;

    /** @var array|false */
    public $datalist = false;

    /**
     * @param string $owningClass
     */
    public function __construct(string $owningClass)
    {
        $classWithoutNamespace = substr($owningClass, strlen(__NAMESPACE__) + 1);
        $type = \Deform\Util\Strings::separateCased($classWithoutNamespace, '-');
        $this->containerType = 'container-type-' . $type;
        $this->control = new ComponentControls();
    }

    /**
     * @param string $newId
     * @param string $newName
     * @throws DeformException
     */
    public function changeNamespacedAttributes(string $newId, string $newName): void
    {
        $this->control->changeNamespacedAttributes($newId, $newName);
        if ($this->labelTag) {
            $this->labelTag->set('for', $newId);
        }
    }

    /**
     * @param string $label
     * @param bool $required
     * @throws DeformException
     */
    public function setLabel(string $label, bool $required = false): void
    {
        $this->label = $label;
        $this->labelTag = Html::label()->add($label);
        if ($required) {
            $requiredTag = Html::span(['class' => 'required'])->add("*");
            $this->labelTag->add($requiredTag);
        }
    }

    /**
     * @param string $tooltip
     */
    public function setTooltip(string $tooltip): void
    {
        $this->tooltip = $tooltip;
    }

    /**
     * @param string $hint
     */
    public function setHint(string $hint): void
    {
        $this->hint = $hint;
        $this->hintTag = $hint;
    }

    /**
     * @param string $error
     */
    public function setError(string $error): void
    {
        $this->errorTag = $error;
    }

//    /**
//     * @param array $datalist
//     * @throws DeformException
//     */
//    public function setDatalist(array $datalist)
//    {
//        if (count($this->control->getControls()) > 1) {
//            throw new DeformException("A datalist only makes sense when there is a single control");
//        }
//        $this->datalist = $datalist;
//    }

    /**
     * @param string $fieldName
     * @param string $getExpectedDataName
     */
    public function addExpectedInput(string $fieldName, string $getExpectedDataName): void
    {
        $this->expectedInput = Html::input([
            "type" => "hidden",
            "name" => $getExpectedDataName,
            "value" => $fieldName
        ]);
    }

    /**
     * this is where everything is put together and ensures a consistent structure
     * @param string $containerId
     * @param array $attributes
     * @return HtmlTag
     * @throws DeformException
     */
    public function generateHtmlTag(string $containerId, array $attributes = []): IHtml
    {
        $controls = $this->control->getControls();
        if (count($controls) == 0) {
            throw new DeformComponentException(
                "Components must contain at least one control : " . $this->containerType
            );
        }

        if ($this->controlOnly) {
            if (count($controls) > 1) {
                throw new DeformComponentException(
                    "Multiple tags for control-only type containers is not currently supported!"
                );
            }
            $htmlTag = $controls[0];
            if (count($attributes) > 0) {
                $htmlTag->setMany($attributes);
            }
            return $htmlTag;
        }

        $containerAttributes = [
            'id' => $containerId,
            'class' => 'component-container ' . $this->containerType
        ];
        if ($this->tooltip) {
            $containerAttributes['title'] = $this->tooltip;
        }
        $htmlContainer = Html::div($containerAttributes);

        if ($this->labelTag && !$this->disableLabel) {
            if (!is_bool($this->labelTag) && ($this->labelTag instanceof HtmlTag) && (!$this->labelTag->has('for'))) {
                if (count($controls) == 1) {
                    // if the label tag is present and hasn't yet got a for attribute then guess it!
                    $labelFor = $this->guessLabelFor($controls[0]);
                    if ($labelFor) {
                        $this->labelTag->set('for', $labelFor);
                    }
                }
            }
            $labelContainer = Html::div($this->labelContainerAttributes)->add($this->labelTag);
            $htmlContainer->add($labelContainer);
        }

        $controls = $this->control->getControls();
        if (count($attributes) > 0) {
            foreach ($controls as $control) {
                $control->setMany($attributes);
            }
        }
        $controlContainer = Html::div($this->controlContainerAttributes);
        foreach ($this->control->getHtmlTags() as $tag) {
            $controlContainer->add($tag);
        }
        $htmlContainer->add($controlContainer);

        if (is_array($this->datalist) && count($controls) === 1) {
            $id = $controls[0]->get('name');
            $dataList = Html::datalist()->id($id);
            foreach ($this->datalist as $value) {
                $dataList->add(Html::option()->value($value));
            }
            $controlContainer->add($dataList);
        }

        if ($this->hintTag && !$this->disableHint) {
            $hintContainer = Html::div($this->hintContainerAttributes)->add($this->hintTag);
            $htmlContainer->add($hintContainer);
        }

        if ($this->errorTag && !$this->disableError) {
            $errorContainer = Html::div($this->errorContainerAttributes)->add($this->errorTag);
            $htmlContainer->add($errorContainer);
        }

        if ($this->expectedInput !== null) {
            $htmlContainer->add($this->expectedInput);
        }

        return $htmlContainer;
    }

    /**
     * @param null|HtmlTag|string|array $controlTag
     * @return false|string
     */
    private function guessLabelFor(mixed $controlTag): false|string
    {
        if (!$controlTag) {
            return false;
        }
        $checkTags = is_array($controlTag)
            ? $controlTag
            : [ $controlTag ];
        foreach ($checkTags as $tag) {
            if ($tag instanceof IHtml) {
                if ($tag->has('id')) {
                    return $tag->get('id') ?: false;
                }
            }
        }
        return false;
    }

    /**
     * @param array $attributes
     * @throws DeformException
     */
    public function setControlAttributes(array $attributes): void
    {
        foreach ($this->control->getControls() as $control) {
            $control->setMany($attributes);
        }
    }

    /**
     * @param array $attributes
     * @throws DeformException
     */
    public function setContainerAttributes(array $attributes): void
    {
        foreach ($attributes as $key => $value) {
            switch ($key) {
                case 'label':
                    $this->setLabel($value);
                    break;

                case 'hint':
                    $this->setHint($value);
                    break;

                case 'tooltip':
                    $this->setTooltip($value);
                    break;

                default:
                    throw new DeformComponentException("Unrecognised container attribute '" . $key . "'");
            }
        }
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $array = [];
        if ($this->label) {
            $array['label'] = $this->label;
        }
        if ($this->hint) {
            $array['hint'] = $this->hint;
        }
        if ($this->tooltip) {
            $array['tooltip'] = $this->tooltip;
        }
        return array_filter($array);
    }
}
