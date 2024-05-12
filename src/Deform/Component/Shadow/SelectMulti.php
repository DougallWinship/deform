<?php

declare(strict_types=1);

namespace Deform\Component\Shadow;

trait SelectMulti
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
        let selected;
        try {
             selected = JSON.parse(this.getAttribute('selected'));
        }
        catch(err) {
            selected = [this.getAttribute('selected')];
        }
        let value = [];
        element.querySelectorAll('option').forEach((optionElement) => {
            if (selected.includes(optionElement.value)) {
                optionElement.selected = true;
                value.push(optionElement.value);
            }
        });
        this.internals_.setFormValue(JSON.stringify(value));
    }
JS
            ] + parent::shadowJavascript();
    }
}
