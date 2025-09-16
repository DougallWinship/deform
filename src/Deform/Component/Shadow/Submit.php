<?php

declare(strict_types=1);

namespace Deform\Component\Shadow;

trait Submit
{
    public function getShadowMethods(): string
    {
        return <<<JS
initValue(element, value)
{
    element.value = value
    element.addEventListener('click', ()=> {
        this.emitEvent('change', element.value);
        this.internals_.setFormValue(element.value);
    });
}
updateValue(element, value)
{
    element.value = value
    this.internals_.setFormValue(element.value);
}
JS;
    }

    public function mergeShadowAttributes(&$attributes): void
    {
        $attributes['value'] = new Attribute(
            "value",
            "input",
            Attribute::TYPE_STRING,
            "this.initValue(element, this.getAttribute('value'));",
            "this.updateValue(element, newValue);",
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
