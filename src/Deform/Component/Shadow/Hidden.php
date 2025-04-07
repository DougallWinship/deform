<?php

declare(strict_types=1);

namespace Deform\Component\Shadow;

trait Hidden
{
    public function getShadowMethods(): string
    {
        return <<<JS
initValue(element) 
{
    element.value = this.getAttribute('value'); 
    this.internals_.setFormValue(element.value); 
    element.addEventListener('change', ()=> { 
        if (this.getAttribute('value')!==element.value) { 
            this.setAttribute('value',element.value);
            this.internals_.setFormValue(element.value);
        }
    });
}
JS;
    }

    public function mergeShadowAttributes(): array
    {
        $attributes = [];
        $attributes['label'] = false;
        $attributes['hint'] = false;
        $attributes['error'] = false;
        $attributes['required'] = false;
        $attributes['name'] = new Attribute(
            'name',
            'input',
            Attribute::TYPE_STRING,
            "element.name = this.getAttribute('name');",
            "element.name = newValue;",
        );
        $attributes['value'] = new Attribute(
            'value',
            'input',
            Attribute::TYPE_STRING,
            "this.initValue(element);",
            "element.value = newValue;this.internals_.setFormValue(element.value);",
        );
        return $attributes;
    }
}
