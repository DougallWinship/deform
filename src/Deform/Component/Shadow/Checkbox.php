<?php

declare(strict_types=1);

namespace Deform\Component\Shadow;

trait Checkbox
{
    public function getShadowTemplate(): string
    {
        return parent::getShadowTemplate() . "<style>.control-container {display:flex;flex-direction: row}</style>";
    }

    public function getShadowMethods(): string
    {
        return <<<JS
isChecked(element) 
{
    if (!this.hasAttribute('value')) return false;
    const checked = this.getAttribute('value');
    return checked && Deform.isTruthy(checked.toLowerCase());
}
setChecked(element, addEventListener=false) 
{
    if (this.isChecked(element)) {
        this.internals_.setFormValue(this.getAttribute('option') || "on");
        element.checked = true;
    }
    else {
        element.checked = false;
        this.internals_.setFormValue(null);
    }
    if (addEventListener) {
        element.addEventListener('input', ()=> {
            this.setChecked(element, false);
        });
    }
}
JS;
    }

    public function mergeShadowAttributes(): array
    {
        $attributes = [];

        $attributes['value'] = new Attribute(
            'value',
            ".control-container input",
            Attribute::TYPE_BOOLEAN,
            "this.setChecked(element,false)",
            "this.setChecked(element,true)",
            Attribute::BEHAVIOUR_VISIBLE_IF_EMPTY,
        );

        $attributes['option'] = new Attribute(
            "option",
            ".control-container input",
            Attribute::TYPE_STRING,
            "element.value = this.getAttribute('option');",
            "element.value = newValue",
        );

        $attributes['text'] = new Attribute(
            "text",
            ".control-container label",
            Attribute::TYPE_STRING,
            "element.textContent=this.getAttribute('text');",
            "element.textContent=newValue;"
        );

        return $attributes;
    }
}
