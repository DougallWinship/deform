<?php

declare(strict_types=1);

namespace Deform\Component\Shadow;

trait BaseShadow
{
    public function getShadowAttributes(): array
    {
        $attributes = [];

        $attributes["label"] = new Attribute(
            "label",
            ".label-container label",
            Attribute::TYPE_STRING,
            "element.firstChild.textContent = this.getAttribute('label');",
            "element.firstChild.textContent = newValue;"
        );
        $attributes["required"] = new Attribute(
            "required",
            ".label-container label .required",
            Attribute::TYPE_BOOLEAN,
            "element.style.display = Deform.isTruthy(this.getAttribute('required')) ? 'inline-block' : 'none';",
            "element.style.display = Deform.isTruthy(newValue) ? 'inline-block' : 'none';",
            Attribute::BEHAVIOUR_CUSTOM
        );
        $attributes["hint"] = new Attribute(
            "hint",
            ".hint-container",
            Attribute::TYPE_STRING,
            "element.innerHTML = this.getAttribute('hint');",
            "element.innerHTML = newValue"
        );
        $attributes["error"] = new Attribute(
            "error",
            ".error-container",
            Attribute::TYPE_STRING,
            "element.innerHTML = this.getAttribute('error');",
            "element.innerHTML = newValue;"
        );
        $attributes["name"] = new Attribute(
            "name",
            ".control-container input",
            Attribute::TYPE_STRING,
            "element.name = this.getAttribute('name');",
            "element.name = newValue;"
        );
        $initJS = <<<JS
element.value = this.getAttribute('value'); 
this.internals_.setFormValue(element.value); 
element.addEventListener('change', ()=> { 
    if (this.getAttribute('value')!==element.value) { 
        this.setAttribute('value',element.value); 
    } 
    this.internals_.setFormValue(element.value);
});
JS;
        $updateJS = <<<JS
if (element.value!==newValue) { 
    element.value = newValue;
    this.internals_.setFormValue(element.value);
}
JS;
        $attributes["value"] = new Attribute(
            "value",
            ".control-container input",
            Attribute::TYPE_STRING,
            $initJS,
            $updateJS,
            Attribute::BEHAVIOUR_VISIBLE_IF_EMPTY
        );
        $this->mergeShadowAttributes($attributes);
        return array_filter($attributes);
    }

    public function mergeShadowAttributes(array &$attributes): void
    {
    }

    public function getShadowMethods(): string
    {
        return "";
    }

    public function getAdditionalAttributes(): array
    {
        return [];
    }
}
