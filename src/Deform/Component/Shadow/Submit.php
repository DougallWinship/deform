<?php

declare(strict_types=1);

namespace Deform\Component\Shadow;

trait Submit
{
    public function mergeShadowAttributes(): array
    {
        $attributes = [];
        $attributes['value'] = new Attribute(
            "value",
            "input",
            Attribute::TYPE_STRING,
            "element.value = this.getAttribute('value'); this.internals_.setFormValue(element.value);",
        );
        $attributes['hint'] = null;
        $attributes['error'] = null;
        $attributes['label'] = null;
        $attributes['required'] = null;

        $attributes["name"] = new Attribute(
            "name",
            "input",
            Attribute::TYPE_STRING,
            "element.name = this.getAttribute('name');",
            "element.name = newValue;"
        );

        return $attributes;
    }
}
