<?php
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

    /** @var mixed */
    public $tooltip = false;

    public string $containerType;

    public array $labelContainerAttributes = ['class' => 'label-container'];
    /** @var mixed */
    public $labelTag = false;
    public $disableLabel = false;

    public array $controlContainerAttributes = ['class' => 'control-container'];
    /** @var mixed */
    public $controlTag = false;

    public array $hintContainerAttributes = ['class' => 'hint-container'];
    /** @var mixed */
    public $hintTag = false;
    public $disableHint = false;

    public array $errorContainerAttributes = ['class' => 'error-container'];
    /** @var mixed */
    public $errorTag = false;
    public $disableError = false;

    public function __construct($owningClass)
    {
        if (strpos($owningClass,__NAMESPACE__)!==0) {
            throw new \Exception("ComponentContainer unexpectedly used from class in different namespace : ".$owningClass);
        }
        $classWithoutNamespace = substr($owningClass,strlen(__NAMESPACE__)+1);
        $type = \Deform\Util\Strings::separateCased($classWithoutNamespace,'-');
        $this->containerType = 'container-type-'.$type;
    }

    /**
     * @param $containerId
     * @return HtmlTag
     * @throws \Exception
     */
    public function getHtmlTag($containerId) : IHtml
    {
        if ($this->controlOnly) {
            if (is_array($this->controlTag)) {
                throw new \Exception("Multiple tage for control-only type containers is not currently supported!");
                //return Html::div(['class' => 'control-container '.$this->containerType])->add($this->controlTag);
            }
            return $this->controlTag;
        }

        $attrs = [
            'id' => $containerId,
            'class' => 'component-container '.$this->containerType
        ];
        if ($this->tooltip) {
            $attrs['title'] = $this->tooltip;
        }
        $htmlContainer = Html::div($attrs);

        if ($this->labelTag && !$this->disableLabel) {
            if (!is_bool($this->labelTag) && ($this->labelTag instanceof HtmlTag) && (!$this->labelTag->has('for'))) {
                // if the label tag is present and hasn't yet got a for attribute then guess it!
                $labelFor = $this->guessLabelFor($this->controlTag);
                if ($labelFor) $this->labelTag->set('for',$labelFor);
            }
            $labelContainer = Html::div($this->labelContainerAttributes)->add($this->labelTag);
            $htmlContainer->add($labelContainer);
        }

        if ($this->controlTag) {
            $controlContainer = Html::div($this->controlContainerAttributes)->add($this->controlTag);
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

    private function guessLabelFor($controlTag)
    {
        if (!$controlTag) return false;
        $checkTags = is_array($controlTag)
            ? $controlTag
            : [ $controlTag ];
        foreach ($checkTags as $tag) {
            if ($tag instanceof IHtml) {
                if ($tag->has('id')) {
                    return $tag->get('id');
                }
            }
        }
        return false;
    }
}
