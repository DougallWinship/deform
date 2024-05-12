<?php

declare(strict_types=1);

namespace Deform\Component;

use Deform\Html\Html as Html;
use Deform\Html\HtmlTag;

/**
 * @persistAttribute radioButtons
 */
class RadioButtonSet extends BaseComponent
{
    /** @var array radio button values */
    private array $radioButtons;

    /** @var HtmlTag[] */
    private array $radioButtonInputsByValue = [];

    /**
     * @inheritDoc
     */
    public function setup(): void
    {
        // unusually don't do any setup here as it's only possible to do once the buttons have been specified
    }

    /**
     * @templateMethod
     * @param array $radioButtons
     * @return RadioButtonSet
     * @throws \Exception
     */
    public function radioButtons(array $radioButtons): self
    {
        $isAssoc = \Deform\Util\Arrays::isAssoc($radioButtons);
        $this->radioButtons = $radioButtons;
        foreach ($radioButtons as $key => $value) {
            $radioButtonContainer = Html::div(['class' => 'radiobuttonset-radio-container']);
            $radioLabel = $value;
            $radioValue = $isAssoc ? $key : $value;
            $id = self::getMultiControlId($this->getId(), $radioValue);
            $radioButtonInput = Html::input([
                'type' => 'radio',
                'value' => $radioValue,
                'id' => $id,
                'name' => $this->getName(),
            ]);
            $this->radioButtonInputsByValue[$radioValue] = $radioButtonInput;
            $radioButtonContainer->add($radioButtonInput);
            $radioButtonContainer->add(" ");
            $radioButtonContainer->add(Html::label(['for' => $id])->add($radioLabel));
            $this->addControl($radioButtonInput, $radioButtonContainer);
        }
        $this->addExpectedField($this->fieldName);
        return $this;
    }

    /**
     * @return RadioButtonSet
     */
    public function clearSelected(): self
    {
        foreach ($this->radioButtonInputsByValue as $html) {
            $html->unset('checked');
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setValue($value): self
    {
        foreach ($this->radioButtonInputsByValue as $tagValue => $htmlTag) {
            if ($tagValue === $value) {
                $htmlTag->set('checked', true);
            } else {
                $htmlTag->unset('checked');
            }
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function hydrate()
    {
        if (count($this->radioButtons) > 0) {
            $this->radioButtons($this->radioButtons);
        }
    }

    public function shadowJavascript(): ?array
    {
        return [
            '.control-container .radiobuttonset-radio-container' => <<<JS
let elementsByValue={};
if (this.hasAttribute('options')) {
    let values = JSON.parse(this.getAttribute('options'));
    Object.keys(values).forEach((key) => {
        let radiobuttonWrapper = element.cloneNode(true);
        let radiobuttonInput = radiobuttonWrapper.querySelector('input');
        radiobuttonInput.id = id+'-'+key;
        radiobuttonInput.value = key;
        radiobuttonInput.name = name+"[]";
        let radiobuttonLabel = radiobuttonWrapper.querySelector('label');
        radiobuttonLabel.innerHTML = values[key];
        radiobuttonLabel.setAttribute('for',id+'-'+key);
        element.parentNode.append(radiobuttonWrapper);
        elementsByValue[key] = radiobuttonInput;
    });
}
if (this.hasAttribute('checked')) {
    let checkedValue = this.getAttribute('checked');
    if (checkedValue in elementsByValue) {
        elementsByValue[checkedValue].checked=true;
        this.internals_.setFormValue(checkedValue);
    }
}
element.remove();
JS,
            '.component-container input[type=hidden]' => <<<JS
element.name= (namespaceAttr ? namespaceAttr+'[expected_data][]' : 'expected_data');
element.value = nameAttr;
JS
            ] + parent::shadowJavascript();
    }
}
