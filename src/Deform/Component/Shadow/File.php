<?php

declare(strict_types=1);

namespace Deform\Component\Shadow;

use Deform\Util\Strings;

trait File
{
    public function getShadowMethods(): string
    {
        $fileOrFiles = (Strings::getClassWithoutNamespace(get_called_class()) === "MultipleFile")
            ? "files"
            : "files?.[0]";
        return <<<JS
initialise() 
{
    const element = this.container.querySelector(".control-container input");
    if (element) {
        if (this.form) {
            this.addArrowListener(this.form,'formdata', (evt) => {
                const formData = evt.formData;
                for (let file of element.files) {
                    formData.append(this.getAttribute("name"), file);
                }
            })
        }
        this.addArrowListener(element, 'change', ()=> {
            this.emitEvent("change", element.$fileOrFiles);
        })
    }
    const clearButton = this.container.querySelector("button.clear-button");
    if (clearButton) {
        this.addArrowListener(clearButton, "click", (evt) => {
            this.emitEvent("change", null);
        })
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
            "this.initialise()",
            ""
        );
    }
}
