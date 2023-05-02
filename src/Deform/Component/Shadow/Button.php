<?php

declare(strict_types=1);

namespace Deform\Component\Shadow;

trait Button
{
    public function getShadowTemplate(): string
    {
        return str_replace('</button>', '<slot></slot></button>', parent::getShadowTemplate());
    }

    public function shadowJavascript()
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
JS
            ] + parent::shadowJavascript();
    }

}