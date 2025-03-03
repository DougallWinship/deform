<?php

declare(strict_types=1);

namespace Deform\Component\Shadow;

trait Checkbox
{
    public function getShadowTemplate(): string
    {
        return parent::getShadowTemplate()."<style>.control-container {display:flex;flex-direction: row}</style>";
    }

    public function mergeShadowAttributes(): array
    {
        $attributes = [];
        $initialiseJs = <<<JS
let checked = this.getAttribute('value');
if (!checked || checked.toLowerCase()==='false' || parseInt(checked)===0) {
    element.checked = false;
} 
else {
    element.checked = true;
}
this.internals_.setFormValue(element.value, element.checked ? 'checked':'')
element.addEventListener('input',()=>{
    this.internals_.setFormValue(element.value, element.checked ? 'checked' : ''); 
})
JS;
        $attributes['value'] = new Attribute(
            'value',
            ".control-container input",
            Attribute::TYPE_BOOLEAN,
            $initialiseJs,
            '',
            false
        );

        $attributes['option'] = new Attribute(
            "option",
            ".control-container input",
            Attribute::TYPE_STRING,
            "element.value = this.getAttribute('option'); element.addEventListener('click',()=> { this.internals_.setFormValue(element.value); });",
            "element.setAttribute('value', newValue);"
        );

        $attributes['text'] = new Attribute(
            "text",
            ".control-container label",
            Attribute::TYPE_STRING,
            "element.textContent=this.getAttribute('text');",
            "element.textContent=newValue;"
        );

        $attributes['value'] = false;

        return $attributes;
    }
}
