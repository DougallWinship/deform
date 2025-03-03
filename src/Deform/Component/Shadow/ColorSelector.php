<?php

declare(strict_types=1);

namespace Deform\Component\Shadow;

trait ColorSelector
{
    public function mergeShadowAttributes(): array
    {
        $attributes = [];

        $initJs = <<<JS
element.value = this.getAttribute('value');
element.addEventListener('input', ()=> { 
    this.internals_.setFormValue(element.value);
});
element.addEventListener('change', ()=> { 
    this.internals_.setFormValue(element.value);
});
JS;
        $attributes["value"] = new Attribute(
            "value",
            ".control-container input",
            Attribute::TYPE_STRING,
            $initJs,
            "element.value = newValue; this.internals_.setFormValue(element.value);"
        );
        return $attributes;
    }
}
