<?php

declare(strict_types=1);

namespace Deform\Component\Shadow;

trait TextArea
{
    public function getShadowTemplate(): string
    {
        return str_replace('</textarea>', '</textarea><slot style="display:none"></slot>', parent::getShadowTemplate());
    }

    public function getAdditionalAttributes(): array
    {
        return [
            "_observer = null",
            "_ignoreSlotChanges = false"
        ];
    }

    public function getShadowMethods(): string
    {
        return <<<JS
initSlot() 
{
    requestAnimationFrame(()=> {
        const textarea = this.shadowRoot.querySelector("textarea");
        
        const updateFromSlot = () => {
            if (this._ignoreSlotChanges) return;
            
            const newValue = [...this.childNodes]
                .filter(n => n.nodeType === Node.TEXT_NODE)
                .map(n => n.textContent)
                .join("")
                .trim();
        
            textarea.value = newValue;
            this.internals_.setFormValue(newValue);
        };
        
        updateFromSlot();
        
        textarea.addEventListener('input', () => {
            this.internals_.setFormValue(textarea.value);
            this._ignoreSlotChanges = true;
            [...this.childNodes].forEach(node => {
                if (node.nodeType === Node.TEXT_NODE) {
                    this.removeChild(node);
                }
            });
            this.appendChild(document.createTextNode(textarea.value));
            this._ignoreSlotChanges = true;
        });
        
        this._observer = new MutationObserver(updateFromSlot);
        this._observer.observe(this, { childList: true, characterData: true, subtree: true });
        
    }); 
}
JS;
    }

    public function mergeShadowAttributes(&$attributes): void
    {
        $attributes['value'] = null;
        $attributes['slot'] = new Attribute(
            "slot",
            Attribute::SLOT_SELECTOR,
            Attribute::TYPE_TEXTAREA,
            "this.initSlot();",
            "",
        );
        $attributes["name"] = new Attribute(
            "name",
            ".component-container textarea",
            Attribute::TYPE_STRING,
            "element.name = this.getAttribute('name');",
            "element.name = newValue;"
        );
    }
}
