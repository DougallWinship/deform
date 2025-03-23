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
        $initJs = <<<JS
if (!this.hasAttribute('required') || !Deform.isTruthy(this.getAttribute('required'))) {
    element.part.remove('deform-hidden');
}
else {
    element.part.add('deform-hidden');
}
JS;
        $updateJs = <<<JS
if (newValue===null || !Deform.isTruthy(newValue)) {
    element.part.add('deform-hidden');
}
else {
    element.part.remove('deform-hidden');
}
JS;
        $attributes["required"] = new Attribute(
            "required",
            ".label-container label .required",
            Attribute::TYPE_BOOLEAN,
            $initJs,
            $updateJs,
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
    this.internals_.setFormValue(element.value); });
JS;
        $updateJS = <<<JS
if (element.value!==newValue) { 
    element.value = newValue; 
} 
this.internals_.setFormValue(element.value);
JS;
        $attributes["value"] = new Attribute(
            "value",
            ".control-container input",
            Attribute::TYPE_STRING,
            $initJS,
            $updateJS,
            Attribute::BEHAVIOUR_VISIBLE_IF_EMPTY
        );
        $mergeAttributes = $this->mergeShadowAttributes();

        // it's necessary to ensure the order specified in mergeShadowAttributes is preserved
        // hence pre-remove any which are to be overwritten
        $removeKeys = array_intersect(array_keys($attributes), array_keys($mergeAttributes));
        foreach ($removeKeys as $key) {
            unset($attributes[$key]);
        }

        foreach ($mergeAttributes as $name => $attribute) {
            if ($attribute) {
                $attributes[$name] = $attribute;
            } else {
                unset($attributes[$name]);
            }
        }
        return $attributes;
    }

    public function mergeShadowAttributes(): array
    {
        return [];
    }

    public function getShadowMethods(): string
    {
        return "";
    }
}
