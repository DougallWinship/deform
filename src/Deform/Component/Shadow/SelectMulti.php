<?php

declare(strict_types=1);

namespace Deform\Component\Shadow;

trait SelectMulti
{
    public function getShadowMethods(): string
    {
        return <<<JS
setOptions(element, value, removeExisting=false) 
{
    let values;
    try {
        values = JSON.parse(this.getAttribute('options'));
    }
    catch (err) {
        console.error("invalid SelectMulti options json : "+value);
        return;
    }
    if (removeExisting) {
        element.parentNode.querySelectorAll('option').forEach(function (element, index) {
            if (index>0) {
                element.remove();
            }
        })
    }
    values.forEach((keyValue) => {
        const option = element.cloneNode(true);
        option.value = keyValue[0];
        option.innerText = keyValue[1];
        option.style.display = "block";
        element.parentNode.appendChild(option);
    });
    element.style.display = 'none';
}

setFormValue(options, name=null) 
{
    let values = [];
    options.forEach((element) => {
        if (element.selected) {
            values.push(element.value);
        }
    })
    this.internals_.setFormValue(JSON.stringify(values));
}

setValues(element, valuesJson) 
{
    let values;
    try {
        values = JSON.parse(valuesJson);
    }
    catch (err) {
        console.error("invalid SelectMulti options json : "+value);
        return;
    }
    let options = element.querySelectorAll('option');
    options.forEach((option, index) => {
        if (index>0) {
            option.selected = values.includes(option.value);
        }
    })
    this.setFormValue(options);
    element.addEventListener('change', () => {
        this.setFormValue(options);
    })
}
setName(element, value) {
    
}
JS;
    }

    public function mergeShadowAttributes(): array
    {
        $attributes = [];

        $attributes['options'] = new Attribute(
            'options',
            '.component-container select option',
            Attribute::TYPE_KEYVALUE_ARRAY,
            "this.setOptions(element, this.getAttribute('options'), true);",
            "this.setOptions(element, newValue, true);"
        );

        $attributes['value'] = new Attribute(
            'value',
            '.component-container select',
            Attribute::TYPE_ARRAY,
            "this.setValues(element, this.getAttribute('value'));",
            "this.setValues(element, newValue);"
        );

        $attributes["name"] = new Attribute(
            "name",
            ".component-container select",
            Attribute::TYPE_STRING,
            "element.name = this.getAttribute('name');",
            "if (oldValue!==newValue) {  }"
        );

        return $attributes;
    }
}
