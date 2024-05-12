<?php

declare(strict_types=1);

namespace Deform\Component\Shadow;

trait Select
{
    public function shadowJavascript(): ?array
    {
        return [
                '.control-container select option' => <<<JS
if (this.hasAttribute('options')) {
    let options = JSON.parse(this.getAttribute('options'));
    Object.keys(options).forEach((key)=> {
      let option = element.cloneNode(true);
      option.value = key;
      option.innerHTML = options[key];
      element.parentNode.append(option);
    })
}
else if (this.hasAttribute('optgroupOptions')) {
    console.log('not yet supported');
}
element.remove();
JS,
                '.control-container select' => <<<JS
    if (this.hasAttribute('selected')) {
        element.value = this.getAttribute('selected');
        this.internals_.setFormValue(element.value);
    }
JS
            ] + parent::shadowJavascript();
    }
}
