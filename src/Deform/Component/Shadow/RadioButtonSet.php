<?php

declare(strict_types=1);

namespace Deform\Component\Shadow;

trait RadioButtonSet
{
    public function shadowJavascript(): ?array
    {
        return [
                '.control-container .radiobuttonset-radio-container' => <<<JS
let elementsByValue={};
if (this.hasAttribute('options')) {
    let values = JSON.parse(this.getAttribute('options'));
    Object.keys(values).forEach((key) => {
        let radiobuttonWrapper = element.cloneNode(true);
        let radiobuttonInput = radiobuttonWrapper.querySelector('input');
        radiobuttonInput.id = id+'-'+key;
        radiobuttonInput.value = key;
        radiobuttonInput.name = name+"[]";
        let radiobuttonLabel = radiobuttonWrapper.querySelector('label');
        radiobuttonLabel.innerHTML = values[key];
        radiobuttonLabel.setAttribute('for',id+'-'+key);
        element.parentNode.append(radiobuttonWrapper);
        elementsByValue[key] = radiobuttonInput;
    });
}
if (this.hasAttribute('checked')) {
    let checkedValue = this.getAttribute('checked');
    if (checkedValue in elementsByValue) {
        elementsByValue[checkedValue].checked=true;
        this.internals_.setFormValue(checkedValue);
    }
}
element.remove();
JS,
                '.component-container input[type=hidden]' => <<<JS
element.name= (namespaceAttr ? namespaceAttr+'[expected_data][]' : 'expected_data');
element.value = nameAttr;
JS
            ] + parent::shadowJavascript();
    }

    public function mergeAttributeMetadata(): array
    {
        return [
            'options' => 'json',
            'checked' => 'string'
        ];
    }
}