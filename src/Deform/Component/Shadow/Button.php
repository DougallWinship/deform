<?php

declare(strict_types=1);

namespace Deform\Component\Shadow;

trait Button
{
    public function getShadowTemplate(): string
    {
        return str_replace('</button>', '<slot></slot></button>', parent::getShadowTemplate());
    }

    public function getShadowMethods(): string
    {
        return <<<JS
setValue(element, value, initialise) 
{
    element.value = this.getAttribute('value');
    if (initialise) {
        this.addArrowListener(element, 'click', ()=> { 
            this.internals_.setFormValue(element.value);
            this.emitEvent('change', element.value);
        });
    }
}
JS;
    }

    public function mergeShadowAttributes(&$attributes): void
    {
        $attributes["name"] = new Attribute(
            "name",
            ".control-container button",
            Attribute::TYPE_STRING,
            "element.name = this.getAttribute('name');",
            "element.name = newValue;"
        );

        $attributes["value"] = new Attribute(
            "value",
            ".control-container button",
            Attribute::TYPE_STRING,
            "this.setValue(element, this.getAttribute('value'), true);",
            "this.setValue(element, newValue, false);",
            default:"1"
        );

        $attributes['slot'] = new Attribute(
            "slot",
            Attribute::SLOT_SELECTOR,
            Attribute::TYPE_STRING
        );
    }
}
