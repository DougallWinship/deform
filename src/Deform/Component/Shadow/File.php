<?php

declare(strict_types=1);

namespace Deform\Component\Shadow;

trait File
{
    public function mergeShadowAttributes(&$attributes): void
    {
        $initJs = <<<JS
const element = this.container.querySelector(".control-container input");

if (element) {
    this.form?.addEventListener("formdata", (evt)=> {
        const formData = evt.formData;
        for (let file of element.files) {
            formData.append(this.getAttribute("name"), file);
        }
    });
}
JS;
        $attributes['value'] = new Attribute(
            "value",
            Attribute::SLOT_SELECTOR,
            Attribute::TYPE_FILE,
            $initJs,
        );
    }
}
