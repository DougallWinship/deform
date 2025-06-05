<?php

declare(strict_types=1);

namespace Deform\Component\Shadow;

trait RadioButtonSet
{
    public function getShadowMethods(): string
    {
        return <<<JS
setOptions(element, value, initialise=false)
{
    let values;
    try {
        values = JSON.parse(value);
    }
    catch (err) {
        console.error("invalid RadioButtonSet options json : "+value);
        return;
    }

    if (initialise) {
        element.style.display='none';
    }
    else {
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
        radiobuttonWrapper.style.display='flex';
        let radiobuttonInput = radiobuttonWrapper.querySelector('input');
        radiobuttonInput.id = 'radiobutton-'+key;
        radiobuttonInput.value = key;
        radiobuttonInput.name = radiobuttonInput.name+"[]";
        radiobuttonInput.style.display = 'inline-block';
        radiobuttonInput.addEventListener('change', () => {
            this.internals_.setFormValue(value); 
            this.setAttribute('value', value);
        });
        let radiobuttonLabel = radiobuttonWrapper.querySelector('label');
        radiobuttonLabel.innerHTML = value;
        radiobuttonLabel.setAttribute('for','radiobutton-'+key);
        radiobuttonLabel.style.display = 'inline-block';
        element.parentNode.append(radiobuttonWrapper);
    });
}
setValue(checked) 
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
        }
    });
    this.internals_.setFormValue(checkedValue);    
}
JS;
    }
    public function mergeShadowAttributes(&$attributes): void
    {
        $attributes['options'] = new Attribute(
            'options',
            '.control-container .radiobuttonset-radio-container',
            Attribute::TYPE_KEYVALUE_ARRAY,
            "this.setOptions(element, this.getAttribute('options'), true);",
            "this.setOptions(element, newValue, false);",
            Attribute::BEHAVIOUR_CUSTOM
        );
        $attributes['value'] = new Attribute(
            'value',
            '.control-container .radiobuttonset-radio-container',
            Attribute::TYPE_STRING,
            "this.setValue(this.getAttribute('value'));",
            "this.setValue(newValue);"
        );
    }
}
