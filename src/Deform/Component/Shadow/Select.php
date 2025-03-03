<?php

declare(strict_types=1);

namespace Deform\Component\Shadow;

trait Select
{
    public function mergeShadowAttributes(): array
    {
        $attributes = [];

        $initJs = <<<JS
const values = JSON.parse(this.getAttribute('options'));

values.forEach((keyValue) => {
    const key = keyValue[0];
    const value = keyValue[1];
    const option = element.cloneNode(true);
    option.value = key
    option.innerText = value;
    element.parentNode.appendChild(option);
});
element.style.display = 'none';
JS;
        $attributes['options'] = new Attribute(
            'options',
            '.component-container select option',
            Attribute::TYPE_KEYVALUE_ARRAY,
            $initJs,
            ''
        );

        $initJs = <<<JS
element.value = this.getAttribute('value'); 
this.internals_.setFormValue(element.value);
element.addEventListener('change', () => {
    this.internals_.setFormValue(element.value);
})
JS;

        $attributes['value'] = new Attribute(
            'value',
            '.component-container select',
            Attribute::TYPE_STRING,
            $initJs,
            ''
        );

        $attributes['name'] = new Attribute(
            'name',
            '.component-container select',
            Attribute::TYPE_STRING,
            "element.name = this.getAttribute('name');",
            ''
        );

        //$attributes['value'] = false;

        return $attributes;
    }
}
