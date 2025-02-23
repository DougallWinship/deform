<?php

declare(strict_types=1);

namespace Deform\Component\Shadow;

trait Input
{
    public function shadowJavascript(): array
    {
        return [
                '.control-container input' => <<<JS
element.id = id;
element.name = name;
if (this.hasAttribute('value')) {
    let value = this.getAttribute('value');
    element.value = value;
    this.internals_.setFormValue(value);
}
element.addEventListener('change',()=> {
    this.internals_.setFormValue(element.value);
})
JS
            ] + parent::shadowJavascript();
    }

    public function mergeAttributeMetadata(): array {
        return [
            'value' => 'string'
        ];
    }

}
