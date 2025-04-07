<?php

declare(strict_types=1);

namespace Deform\Component\Shadow;

trait Select
{
    public function getShadowMethods(): string
    {
        return <<<JS
setOptions(selectElement, optionsJson, removeExisting = false) 
{
    const options = Deform.parseJson(optionsJson, "Failed to parse Select 'options'");
    if (options===null) {
        return null;
    }
    if (removeExisting) {
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
        option.value = key
        option.innerText = value;
        option.part.remove('deform-hidden');
        selectElement.appendChild(option);
    });
    templateOption.part.add('deform-hidden');
    selectElement.value = selected;
}
setValue(selectElement, value, addEventListener=false) 
{
    selectElement.value = value;
    this.internals_.setFormValue(selectElement.value);
    if (addEventListener) {
        selectElement.addEventListener('change', () => {
            this.setAttribute('value', selectElement.value);
            this.internals_.setFormValue(selectElement.value);
        })
    }
}
JS;
    }

    public function mergeShadowAttributes(): array
    {
        $attributes = [];

        $attributes['options'] = new Attribute(
            'options',
            '.component-container select',
            Attribute::TYPE_KEYVALUE_ARRAY,
            "this.setOptions(element, this.getAttribute('options'))",
            "this.setOptions(element, newValue, true)"
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
            "this.setValue(element, this.getAttribute('value'), true);",
            "this.setValue(element, newValue);"
        );

        return $attributes;
    }
}
