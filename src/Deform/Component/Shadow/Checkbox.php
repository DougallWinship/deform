<?php

declare(strict_types=1);

namespace Deform\Component\Shadow;

trait Checkbox
{
    /**
     * @return string[]
     * @noinspection
     */
    public function shadowJavascript(): array
    {
        return
            [
                '.control-container label' => <<<JS
if (this.hasAttribute('text')) {
    element.innerHTML = this.getAttribute('text')
}
element.setAttribute('for', id);
JS,
                '.control-container input' => <<<JS
element.id = id;
element.name = name;
if (this.hasAttribute('value')) {
    element.value = this.getAttribute('value');
}
if (this.hasAttribute('checked')) {
    let checked = this.getAttribute('checked');
    if (checked.toLowerCase()==='false' || parseInt(checked)===0) {
        element.checked = false;
    } 
    else {
        element.checked = true;
        this.internals_.setFormValue(element.value, element.checked ? 'checked':'')
    }
}
else {
    element.checked = false
}

element.addEventListener('input',(evt)=>{
    this.internals_.setFormValue(element.value, element.checked ? 'checked' : ''); 
})
JS,
                /* promote the hidden input to the form and remove from the shadowdom */
                '.component-container input[type=hidden]' => <<<JS
element.name=(namespaceAttr ? namespaceAttr+'[expected_data][]' : 'expected_data');
element.value=nameAttr;
this.internals_.form.appendChild(element.cloneNode(false));
element.parentElement.removeChild(element);
JS
            ] + parent::shadowJavascript();
    }

    public function mergeAttributeMetadata(): array {
        return [
            'checked' => 'boolean'
        ];
    }
}
