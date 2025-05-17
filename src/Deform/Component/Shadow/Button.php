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
initValue(element) 
{
    element.value = this.getAttribute('value');
    element.addEventListener('click', ()=> { 
        this.internals_.setFormValue(element.value);
    });
}
initName(element)
{
    element.value = this.getAttribute('value'); 
    this.internals_.setFormValue(element.value); 
    element.addEventListener('change', ()=> { 
        if (this.getAttribute('value')!==element.value) { 
            this.setAttribute('value',element.value); 
        } 
        this.internals_.setFormValue(element.value); 
    });
}
updateName(element, newValue)
{
    if (element.value!==newValue) { 
        element.value = newValue;
        this.internals_.setFormValue(element.value);
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
            "this.initName(element);",
            "this.updateName(element, newValue);"
        );

        $attributes["value"] = new Attribute(
            "value",
            ".control-container button",
            Attribute::TYPE_STRING,
            "this.initValue(element)",
            "element.value = newValue; this.internals_.setFormValue(element.value);"
        );

        $attributes['slot'] = new Attribute(
            "slot",
            Attribute::SLOT_SELECTOR,
            Attribute::TYPE_STRING,
        );
    }
}
