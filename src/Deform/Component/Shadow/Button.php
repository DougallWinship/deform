<?php

declare(strict_types=1);

namespace Deform\Component\Shadow;

trait Button
{
    public function getShadowTemplate(): string
    {
        return str_replace('</button>', '<slot></slot></button>', parent::getShadowTemplate());
    }

    public function mergeShadowAttributes(): array
    {
        $attributes = [];

        $attributes["name"] = new Attribute(
            "name",
            ".control-container button",
            Attribute::TYPE_STRING,
            "element.name = this.getAttribute('name');",
            "element.name = newValue; if (name==='name' && oldValue!==newValue) { this.internals_.setFormValue(null, oldValue); this.internals_.setFormValue(element.value || '',newValue); };",
        );

        $initJs = <<<JS
element.value = this.getAttribute('value');
element.addEventListener('click', ()=> { 
    this.internals_.setFormValue(element.value);
});
JS;
        $attributes["value"] = new Attribute(
            "value",
            ".control-container button",
            Attribute::TYPE_STRING,
            $initJs,
            "element.value = newValue; this.internals_.setFormValue(element.value);"
        );
        $attributes['slot'] = new Attribute(
            "slot",
            Attribute::SLOT_SELECTOR,
            Attribute::TYPE_STRING,
        );
        return $attributes;
    }
}
