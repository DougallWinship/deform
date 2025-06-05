<?php

declare(strict_types=1);

namespace Deform\Component\Shadow;

trait Select
{
    public function getShadowMethods(): string
    {
        return <<<JS
setOptions(selectElement, optionsJson, initialise = false) 
{
    const options = Deform.parseJson(optionsJson, "Failed to parse Select 'options'");
    if (options===null) {
        return null;
    }
    if (initialise) {
        selectElement.firstChild.style.display = 'none';
        selectElement.addEventListener('change',()=> {
            this.internals_.setFormValue(selectElement.value);
            this.setAttribute('value', selectElement.value);
        })
    }
    else {
        Array.from(selectElement.children).forEach((child, index) => {
            if (index>0) {
                child.remove();
            }
        });
    }
    const selected = selectElement.getAttribute('value');
    const templateOption = selectElement.firstChild;
    options.forEach((keyValue) => {
        const key = keyValue[0];
        const value = keyValue[1];
        const option = templateOption.cloneNode(true);
        option.value = key;
        option.innerText = value;
        option.style.display = 'block';
        selectElement.appendChild(option);
        
    });
    selectElement.value = selected;
}
setValue(selectElement, value) 
{
    selectElement.value = value;
    this.internals_.setFormValue(selectElement.value);
}
JS;
    }

    public function mergeShadowAttributes(&$attributes): void
    {
        $attributes['options'] = new Attribute(
            'options',
            '.component-container select',
            Attribute::TYPE_KEYVALUE_ARRAY,
            "this.setOptions(element, this.getAttribute('options'),true);",
            "this.setOptions(element, newValue, false);"
        );

        $attributes['name'] = new Attribute(
            'name',
            '.component-container select',
            Attribute::TYPE_STRING,
            "element.name = this.getAttribute('name');",
            "element.name = newValue"
        );

        $attributes['value'] = new Attribute(
            'value',
            '.component-container select',
            Attribute::TYPE_STRING,
            "this.setValue(element, this.getAttribute('value'));",
            "this.setValue(element, newValue);",
        );
    }
}
