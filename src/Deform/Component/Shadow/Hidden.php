<?php

declare(strict_types=1);

namespace Deform\Component\Shadow;

trait Hidden
{
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
            "element.name = this.getAttribute('name');this.internals_.setFormValue(element.name);",
            "element.name = newValue;",
        );
        $attributes['value'] = new Attribute(
            'value',
            'input',
            Attribute::TYPE_STRING,
            "element.value = this.getAttribute('value');",
            "element.value = newValue;",
        );
        return $attributes;
    }
}
