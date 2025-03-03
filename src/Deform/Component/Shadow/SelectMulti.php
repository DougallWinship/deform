<?php

declare(strict_types=1);

namespace Deform\Component\Shadow;

trait SelectMulti
{

    public function mergeShadowAttributes(): array
    {
        $attributes = [];

        $initJs = <<<JS
const values = JSON.parse(this.getAttribute('options'));
Object.keys(values).forEach((key, index) => {
    const option = element.cloneNode(true);
    option.value = key
    option.innerText = values[key];
    if (index===0) {
        option.selected = true;
    }
    element.parentNode.appendChild(option);
});
element.style.display = 'none';
JS;
        $attributes['options'] = new Attribute(
            'options',
            '.component-container select option',
            Attribute::TYPE_JSON_ARRAY,
            $initJs,
            ''
        );

        $initJs = <<<JS
const selected = JSON.parse(this.getAttribute('value'));
const options = element.querySelectorAll('option');

const setFormValue = (options) => {
    let values = [];
    options.forEach((element) => {
        if (element.selected) {
            values.push(element.value);
        }
    })
    this.internals_.setFormValue(JSON.stringify(values));
}

options.forEach((option, index) => {
    if (index>0) {
        if ( selected.includes(option.value)) {
            option.selected = true;
        }
        else {
            option.selected = false;
        }
    }
});
setFormValue(options);
element.addEventListener('change', () => { setFormValue(options); });
JS;
        $attributes['value'] = new Attribute(
            'value',
            '.component-container select',
            Attribute::TYPE_STRING,
            $initJs,
            ''
        );

        $attributes["name"] = new Attribute(
            "name",
            ".component-container select",
            Attribute::TYPE_STRING,
            "element.name=this.getAttribute('name');"
        );

        return $attributes;
    }
}
