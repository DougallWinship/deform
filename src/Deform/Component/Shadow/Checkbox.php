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
        this.internals_.setFormValue('');
    }
    if (addEventListener) {
        element.addEventListener('input', (evt)=> {
            if (evt.originalTarget.checked) {
                this.internals_.setFormValue(this.getAttribute('option') || "on");
                this.setAttribute('value', this.getAttribute('option') || "on");
            }
            else {
                this.internals_.setFormValue('');
                this.setAttribute('value', '');
            }
        });
    }
}
JS;
    }

    public function mergeShadowAttributes(array &$attributes): void
    {
        $attributes['value'] = new Attribute(
            'value',
            ".control-container input",
            Attribute::TYPE_BOOLEAN,
            "console.log('initjs'); this.setChecked(element,true);",
            "console.log('updatejs'); this.setChecked(element,false);",
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
    }
}
