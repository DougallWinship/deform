<?php

declare(strict_types=1);

namespace Deform\Component\Shadow;

trait TextArea
{
    public function getShadowTemplate(): string
    {
        return str_replace('</textarea>', '<slot></slot></textarea>', parent::getShadowTemplate());
    }

    public function mergeShadowAttributes(): array
    {
        $initJs = <<<JS
requestAnimationFrame(()=> {
    let content = "";
    if (this.hasAttribute("value")) {
        content = this.getAttribute("value");
    }
    else if (this.childNodes.length > 0) {
        content = [...this.childNodes]
            .filter(node => node.nodeType === Node.TEXT_NODE) // Only text nodes
            .map(node => node.textContent)
            .join("");
    }
    let textArea = this.template.querySelector('textarea');
    textArea.value = content;
    this.internals_.setFormValue(textArea.value);
    textArea.addEventListener('input', () => {
        this.internals_.setFormValue(textArea.value);
    });
});
JS;

        $attributes = [];
        $attributes['value'] = new Attribute(
            "value",
            Attribute::SLOT_SELECTOR,
            Attribute::TYPE_STRING,
            $initJs,
            ""
        );

        $attributes["name"] = new Attribute(
            "name",
            ".component-container textarea",
            Attribute::TYPE_STRING,
            "element.name = this.getAttribute('name')",
            "element.name = newValue; if (name==='name' && oldValue!==newValue) { this.internals_.setFormValue(null, oldValue); this.internals_.setFormValue(element.value || '',newValue); };"
        );
        return $attributes;
    }
}
