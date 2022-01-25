<?php

declare(strict_types=1);

namespace Deform\Component;

use Deform\Html\Html as Html;
use Deform\Html\HtmlTag;
use Deform\Html\IHtml;

/**
 * defines a standard wrapper for components (see ReadMe.md for more details)
 *
 * todo: - should be able to alter container tags
 */
class ComponentContainer
{
    /** @var bool */
    public bool $controlOnly = false;

    /** @var bool|string */
    public $tooltip = false;

    /** @var string */
    public string $containerType;

    /** @var array|string[] */
    public array $controlContainerAttributes = ['class' => 'control-container'];

    /** @var bool|IHtml */
    public $controlTag = false;
    /** @var bool|array  */
    public $controlTagDecorator = false;

    /** @var array|string[]  */
    public array $labelContainerAttributes = ['class' => 'label-container'];
    /** @var null|string  */
    public ?string $label = null;
    /** @var bool|IHtml */
    public $labelTag = false;
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

    /**
     * @param string $owningClass
     * @throws \Exception
     */
    public function __construct(string $owningClass)
    {
        if (strpos($owningClass, __NAMESPACE__) !== 0) {
            throw new \Exception(
                "ComponentContainer unexpectedly used from class in different namespace : " . $owningClass
            );
        }
        $classWithoutNamespace = substr($owningClass, strlen(__NAMESPACE__) + 1);
        $type = \Deform\Util\Strings::separateCased($classWithoutNamespace, '-');
        $this->containerType = 'container-type-' . $type;
    }

    /**
     * @param string $newId
     * @param string $newName
     * @throws \Exception
     */
    public function changeNamespaceAttributes(string $newId, string $newName)
    {
        if ($this->controlTag) {
            if (is_array($this->controlTag)) {
                $scanControls = $this->controlTag;
            } elseif ($this->controlTag instanceof HtmlTag) {
                $scanControls = [$this->controlTag];
            } else {
                throw new \Exception("Unexpected control tag type '" . gettype($this->controlTag) . "'");
            }
            foreach ($scanControls as $control) {
                if ($control instanceof HtmlTag) {
                    $control->setIfExists('id', $newId);
                    $control->setIfExists('for', $newId);
                    $control->setIfExists('name', $newName);
                }
            }
        }
        if ($this->labelTag) {
            $this->labelTag->set('for', $newId);
        }
    }

    /**
     * @param string $label
     * @throws \Exception
     */
    public function setLabel(string $label)
    {
        $this->label = $label;
        $this->labelTag = Html::label(['style' => 'margin-bottom:0'])->add($label);
    }

    /**
     * @param string $tooltip
     */
    public function setTooltip(string $tooltip)
    {
        $this->tooltip = $tooltip;
    }

    /**
     * @param string $hint
     */
    public function setHint(string $hint)
    {
        $this->hint = $hint;
        $this->hintTag = $hint;
    }

    /**
     * @param string $error
     */
    public function setError(string $error)
    {
        $this->errorTag = $error;
    }

    /**
     * @param string $containerId
     * @param array $attributes
     * @return HtmlTag
     * @throws \Exception
     */
    public function generateHtmlTag(string $containerId, array $attributes = []): IHtml
    {
        if ($this->controlTag && count($attributes) > 0) {
            $this->controlTag->setMany($attributes);
        }
        if ($this->controlOnly) {
            if (is_array($this->controlTag)) {
                throw new \Exception("Multiple tags for control-only type containers is not currently supported!");
            }
            return $this->controlTag;
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
                // if the label tag is present and hasn't yet got a for attribute then guess it!
                $labelFor = $this->guessLabelFor($this->controlTag);
                if ($labelFor) {
                    $this->labelTag->set('for', $labelFor);
                }
            }
            $labelContainer = Html::div($this->labelContainerAttributes)->add($this->labelTag);
            $htmlContainer->add($labelContainer);
        }

        if ($this->controlTag) {
            $controlContainer = Html::div($this->controlContainerAttributes);
            if ($this->controlTagDecorator && count($this->controlTagDecorator) > 0) {
                foreach ($this->controlTagDecorator as $decoratorPart) {
                    $controlContainer->add($decoratorPart);
                }
            } else {
                $controlContainer->add($this->controlTag);
            }
            $htmlContainer->add($controlContainer);
        }

        if ($this->hintTag && !$this->disableHint) {
            $hintContainer = Html::div($this->hintContainerAttributes)->add($this->hintTag);
            $htmlContainer->add($hintContainer);
        }

        if ($this->errorTag && !$this->disableError) {
            $errorContainer = Html::div($this->errorContainerAttributes)->add($this->errorTag);
            $htmlContainer->add($errorContainer);
        }

        return $htmlContainer;
    }

    /**
     * @param bool|HtmlTag $controlTag
     * @return false|string|null
     */
    private function guessLabelFor($controlTag)
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

    public function setControlAttributes($attributes)
    {
        $this->controlTag->setMany($attributes);
    }

    public function setContainerAttributes($attributes)
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
                    throw new \Exception("Unrecognised container attribute '" . $key . "'");
            }
        }
    }

    public function toArray()
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
