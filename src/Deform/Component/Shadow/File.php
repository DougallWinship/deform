<?php

declare(strict_types=1);

namespace Deform\Component\Shadow;

trait File
{
    public function getShadowMethods(): string
    {
        return <<<JS
setValue(initialise) 
{
    const element = this.container.querySelector(".control-container input");
    if (element) {
        this.form?.addEventListener("formdata", (evt)=> {
            const formData = evt.formData;
            for (let file of element.files) {
                formData.append(this.getAttribute("name"), file);
                this.emitEvent("deform:change", file);
            }
        });
    }
}
JS;
    }

    public function mergeShadowAttributes(&$attributes): void
    {
        $attributes['value'] = new Attribute(
            "value",
            Attribute::SLOT_SELECTOR,
            Attribute::TYPE_FILE,
            "this.setValue(true)",
            "this.setValue(false)"
        );
    }
}
