<?php

declare(strict_types=1);

namespace Deform\Component\Shadow;

trait RadioButtonSet
{
    public function getShadowMethods(): string
    {
        return <<<JS
setOptions(element, value, removeExisting=false)
{
    let values;
    try {
        values = JSON.parse(value);
    }
    catch (err) {
        console.error("invalid RadioButtonSet options json : "+value);
        return;
    }
    if (removeExisting) {
        element.parentElement.querySelectorAll('.radiobuttonset-radio-container').forEach((radiobutton, index) => {
            if (index>0) {
                radiobutton.remove();
            }
        });
    }
    values.forEach((keyValue) => {
        const key = keyValue[0];
        const value = keyValue[1];
        const radiobuttonWrapper = element.cloneNode(true);
        let radiobuttonInput = radiobuttonWrapper.querySelector('input');
        radiobuttonInput.id = 'radiobutton-'+key;
        radiobuttonInput.value = key;
        radiobuttonInput.name = radiobuttonInput.name+"[]";
        radiobuttonInput.style.display = 'inline-block';
        let radiobuttonLabel = radiobuttonWrapper.querySelector('label');
        radiobuttonLabel.innerHTML = value;
        radiobuttonLabel.setAttribute('for','radiobutton-'+key);
        radiobuttonLabel.style.display = 'inline-block';
        element.parentNode.append(radiobuttonWrapper);
    });
}
setValue(checked, addEventListener=false) 
{
    const checkboxElements = this.template.querySelectorAll(".component-container input");
    /* let expectedValues = []; */
    let checkedValue = null;
    checkboxElements.forEach((node, index) => {
        if (index>0) {
            const value = node.getAttribute('value');
            if (checked===value) {
                checkedValue=value;
                node.checked = true;
            }
            else {
                node.checked = false;
            }
            if (addEventListener) {
                node.addEventListener('change', () => {
                    this.setAttribute('value', node.value);
                    this.internals_.setFormValue(node.value);
                });
            }
        }
        else {
            node.parentNode.style.display = 'none';
        }
    });
    this.internals_.setFormValue(checkedValue);    
}
JS;
    }
    public function mergeShadowAttributes(): array
    {
        $attributes = [];

        $attributes['options'] = new Attribute(
            'options',
            '.control-container .radiobuttonset-radio-container',
            Attribute::TYPE_KEYVALUE_ARRAY,
            "this.setOptions(element, this.getAttribute('options'));",
            "this.setOptions(element, newValue, true);",
            Attribute::BEHAVIOUR_VISIBLE_IF_EMPTY
        );

        $attributes['value'] = new Attribute(
            'value',
            '.control-container .radiobuttonset-radio-container',
            Attribute::TYPE_STRING,
            "this.setValue(this.getAttribute('value'), true);",
            "this.setValue(newValue);"
        );

        return $attributes;
    }
}
