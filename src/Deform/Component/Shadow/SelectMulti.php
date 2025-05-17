<?php

declare(strict_types=1);

namespace Deform\Component\Shadow;

trait SelectMulti
{
    public function getShadowMethods(): string
    {
        return <<<JS
setOptions(selectElement, value, removeExisting=false) 
{
    let values;
    try {
        values = JSON.parse(this.getAttribute('options'));
    }
    catch (err) {
        console.error("invalid SelectMulti options json : "+value);
        return;
    }
    const options = selectElement.querySelectorAll('option');
    const templateOption = options[0];
    if (removeExisting) {
        options.forEach(function (element, index) {
            if (index>0) {
                element.remove();
            }
        })
    }
    values.forEach((keyValue) => {
        const option = templateOption.cloneNode(true);
        option.value = keyValue[0];
        option.innerText = keyValue[1];
        option.part.remove('deform-hidden');
        selectElement.appendChild(option);
    });
    templateOption.part.add('deform-hidden');
}
setFormValue(options, name=null) 
{
    let values = [];
    options.forEach((element) => {
        if (element.selected) {
            values.push(element.value);
        }
    });
    const valuesJson = JSON.stringify(values);
    this.internals_.setFormValue(valuesJson);
}
setValues(selectElement, valuesJson) 
{
    const values = Deform.parseJson(valuesJson, "invalid SelectMulti options json : "+valuesJson);
    if (values===null) {
        return;
    }
    let options = selectElement.querySelectorAll('option');
    options.forEach((option, index) => {
        if (index>0) {
            option.selected = values.includes(option.value);
        }
    })
    this.setFormValue(options);
    selectElement.addEventListener('change', () => {
        this.setFormValue(options);
    })
}
JS;
    }

    public function mergeShadowAttributes(&$attributes): void
    {
        $attributes['options'] = new Attribute(
            'options',
            '.component-container select',
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
            "element.name = newValue;"
        );
    }
}
