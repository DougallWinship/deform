<?php

declare(strict_types=1);

namespace Deform\Component\Shadow;

trait File
{
    public function mergeShadowAttributes(): array
    {
        $attributes = [];
        $initJs = <<<JS
element = this.container.querySelector(".control-container input");

if (element) {
    element.addEventListener('change',()=> {
        if (element.files.length > 0 ) {
            this.internals_.setFormValue(element.files);
            let filenames = [];
            Array.from(element.files).forEach((file) => {
                filenames.push(file.name);
            })
/*            hiddenElement.value = filenames.join(',');*/
        }
        else {
            this.internals_.setFormValue(null);
/*            hiddenELement.value = "";*/
        }
    });
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
        return $attributes;
    }
}