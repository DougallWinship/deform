<?php

declare(strict_types=1);

namespace Deform\Component\Shadow;

trait Submit
{
    public function mergeShadowAttributes(): array
    {
        $attributes = [];
        $attributes['name'] = new Attribute(
            "name",
            "input",
            Attribute::TYPE_STRING,
            "element.name = this.getAttribute('name');",
            "element.name = newValue; if (name==='name' && oldValue!==newValue) { this.internals_.setFormValue(null, oldValue); this.internals_.setFormValue(element.value || '',newValue); };"
        );
        $attributes['value'] = new Attribute(
          "value",
          "input",
          Attribute::TYPE_STRING,
          "element.value = this.getAttribute('value'); this.internals_.setFormValue(element.value);",
        );
        $attributes['hint'] = null;
        $attributes['error'] = null;
        $attributes['label'] = null;
        return $attributes;
    }
}
