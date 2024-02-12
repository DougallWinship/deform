<?php

declare(strict_types=1);

namespace Deform\Component\Shadow;

trait Slider
{
    public function shadowJavascript(): array
    {
        return [
                '.control-container input' => <<<JS
element.id = id;
element.name = name;
if (this.hasAttribute('value')) {
    element.value = this.getAttribute('value');
    this.internals_.setFormValue(element.value);
}
if (this.hasAttribute('min')) {
    element.setAttribute('min', this.getAttribute('min'))
}
if (this.hasAttribute('max')) {
    element.setAttribute('max', this.getAttribute('max'))
}
if (this.hasAttribute('showOutput')) {
    let showOutput = this.getAttribute('showOutput');
    let showOutputAsInt = parseInt(showOutput);
    if (showOutput.toLowerCase()!=='false' && (isNaN(showOutputAsInt) || showOutputAsInt!==0)) {
        let output = document.createElement('output');
        output.classList.add('slider-output');
        element.addEventListener('input', (evt)=> {
            output.value = evt.target.value;
        });
        element.parentNode.appendChild(output);
    }
}
element.addEventListener('change', ()=> {
    this.internals_.setFormValue(element.value);
})
JS
            ] + parent::shadowJavascript();
    }
}
