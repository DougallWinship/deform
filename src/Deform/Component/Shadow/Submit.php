<?php

declare(strict_types=1);

namespace Deform\Component\Shadow;

trait Submit
{
    public function getShadowMethods(): string
    {
        return <<<JS
setValue(value) {
    
}
JS;
    }

    public function mergeShadowAttributes(&$attributes): void
    {
        $attributes['value'] = new Attribute(
            "value",
            "input",
            Attribute::TYPE_STRING,
            "element.value = this.getAttribute('value'); this.internals_.setFormValue(element.value); this.emitEvent('change', element.value);",
            "element.value = newValue; this.internals_.setFormValue(element.value);this.emitEvent('change', element.value);",
            default: "Submit"
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
    }
}
