<?php

declare(strict_types=1);

namespace Deform\Component;

use Deform\Html\Html as Html;
use Deform\Util\Arrays;

/**
 * @persistAttribute checkboxValues
 */
class CheckboxMulti extends BaseComponent
{
    public array $checkboxes = [];
    public ?array $checkboxValues = null;

    /**
     * @inheritDoc
     */
    public function setup()
    {
    }

    /**
     * @templateMethod
     * @param array $checkboxes
     * @return $this
     * @throws \Exception
     */
    public function checkboxes(array $checkboxes): self
    {
        $this->checkboxValues = $checkboxes;
        $this->checkboxes = [];
        $this->componentContainer->control->reset();
        $isAssoc = Arrays::isAssoc($checkboxes);
        $name = $this->getName() . "[]";
        $id = $this->getId();

        foreach ($checkboxes as $key => $value) {
            $wrapper = Html::div(['class' => 'checkboxmulti-checkbox-wrapper']);
            $checkboxId = self::getMultiControlId($id, (string)$key);
            $checkbox = Html::input([
                'type' => 'checkbox',
                'id' => $checkboxId,
                'name' => $name,
                'value' => ($isAssoc ? $key : $value)
            ]);
            $this->checkboxes[] = $checkbox;
            $wrapper->add($checkbox)
                    ->add(" ")
                    ->add(Html::label(['for' => $checkboxId])->add($value));
            $this->addControl($checkbox, $wrapper);
        }
        // labels are already generated for each of the checkboxes
        $this->autoLabel(false);
        // special hidden field to indicate all checkbox field for which data is to be expected
        // this is necessary since unchecked fields will not send a -ve in the data (they are simply not there!)
        $this->addExpectedField($this->fieldName);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function hydrate()
    {
        if ($this->checkboxValues != null && count($this->checkboxValues) > 0) {
            $this->checkboxes($this->checkboxValues);
        }
    }

    /**
     * @inheritDoc
     */
    public function setValue($value): self
    {
        foreach ($this->checkboxes as $checkbox) {
            $checkboxValue = $checkbox->get('value');
            if (is_array($value) && in_array($checkboxValue, $value)) {
                $checkbox->set('checked', 'checked');
            } else {
                $checkbox->unset('checked');
            }
        }
        return $this;
    }

    /**
     * @return string[]
     */
    public function shadowJavascript(): array
    {
        return[
            '.control-container .checkboxmulti-checkbox-wrapper' => <<<JS
if (this.hasAttribute('values')) {
    let values = JSON.parse(this.getAttribute('values'));
    Object.keys(values).forEach((key) => {
        let checkBoxWrapper = element.cloneNode(true);
        let checkBoxInput = checkBoxWrapper.querySelector('input');
        checkBoxInput.id = id+'-'+key;
        checkBoxInput.value = key;
        checkBoxInput.name = name+"[]";
        let checkBoxLabel = checkBoxWrapper.querySelector('label');
        checkBoxLabel.innerHTML = values[key];
        checkBoxLabel.setAttribute('for',id+'-'+key);
        element.parentNode.append(checkBoxWrapper);
    });
}
element.remove();
JS,
            '.component-container input[type=hidden]' => <<<JS
element.name= (namespaceAttr ? namespaceAttr+'[expected_data][]' : 'expected_data');
element.value = nameAttr;
JS
        ]  + parent::shadowJavascript();
    }
}
