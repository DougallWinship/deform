<?php

declare(strict_types=1);

namespace Deform\Component\Shadow;

trait TextArea
{
    public function shadowJavascript(): array
    {
        return [
                '.control-container textarea' => <<<JS
setTimeout(()=> { 
    this.textarea.textContent = this.textContent;
    this.internals_.setFormValue(this.textContent); 
});
element.addEventListener('change',()=> {
    this.internals_.setFormValue(element.value);
})
JS
            ] + parent::shadowJavascript();
    }

    public function shadowJavascriptProperties(): array
    {
        return ['textarea' => "this.template.querySelector('textarea');"];
    }
}
