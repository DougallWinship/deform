<?php

declare(strict_types=1);

namespace Deform\Component\Shadow;

trait BaseShadow
{
    public function getShadowAttributes(): array {
        $attributes = [];

        $attributes["label"] = new Attribute(
            "label",
            ".label-container label",
            Attribute::TYPE_STRING,
            "element.innerHTML = this.getAttribute('label');",
            "element.innerHTML = newValue;"
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
        $attributes["value"] = new Attribute(
            "value",
            ".control-container input",
            Attribute::TYPE_STRING,
            "element.value = this.getAttribute('value'); this.internals_.setFormValue(element.value); element.addEventListener('change', ()=> { this.internals_.setFormValue(element.value); });",
            "element.value = newValue; this.internals_.setFormValue(element.value);",
            false
        );
        $mergeAttributes = $this->mergeShadowAttributes();

        // it's necessary to ensure the order specified in mergeShadowAttributes is preserved
        // hence pre-remove any which are to be overwritten
        $removeKeys = array_intersect(array_keys($attributes), array_keys($mergeAttributes));
        foreach($removeKeys as $key) {
            unset($attributes[$key]);
        }

        foreach ($mergeAttributes as $name => $attribute) {
            if ($attribute) {
                $attributes[$name] = $attribute;
            }
            else {
                unset($attributes[$name]);
            }
        }
        return $attributes;
    }

    public function mergeShadowAttributes(): array {
        return [];
    }

    public function getShadowMethods(): string {
        return "";
    }
}