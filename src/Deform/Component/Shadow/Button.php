<?php

declare(strict_types=1);

namespace Deform\Component\Shadow;

trait Button
{
    public function getShadowTemplate(): string
    {
        return str_replace('</button>', '<slot></slot></button>', parent::getShadowTemplate());
    }

    public function shadowJavascript(): ?array
    {
        return [
                'button' => <<<JS
element.id = id;
element.name = name;
if (this.hasAttribute('value')) {
    element.value = this.getAttribute('value');
    element.addEventListener("click",()=> { 
        this.internals_.setFormValue(element.value) 
    })
}
if (this.hasAttribute('type')) {
    const button = this.shadowRoot.querySelector('button');
    button.setAttribute('type', this.getAttribute('type'));
}
JS
            ];
    }

    public function getMethods()
    {
        return <<<JS
function setValue(key, value, settingUp=false) {
    let elem;
    switch(key) {
        case 'value':
             elem = this.container.querySelector('button');
             if (elem) {
                 elem.value = value;
             }
             if (settingUp) {
                 elem.addEventListener('click',()=> {
                     this.internals_.setFormValue(element.value);
                 })
             }
             else {
                 this.internals_.setFormValue(element.value) 
             }
             break;
             
         case 'label':
             elem = this.container.querySelector('label');
             if (elem) {
                 elem.inneHTML = elem.value;
             }
             break
            
         case 'error':
             elem = this.container.querySelector('.error-container');
             if (elem) {
                 elem.inneHTML = elem.value;
             }
             break;
             
         case 'hint'
    }
    
}
JS;
    }

    public function mergeAttributeMetadata(): array
    {
        return [
            'slot' => 'string',
            'type' => 'string'
        ];
    }

    public function mergeDynamicAttributes(): array
    {
        $typeJs = <<<JS
const btn = this.container.querySelector('button');
if (btn) {
    btn.setAttribute('type', newValue);
}
JS;
        return [
            "type" => $typeJs
        ];
    }
}
