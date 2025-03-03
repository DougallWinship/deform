<?php

declare(strict_types=1);

namespace Deform\Component\Shadow;

trait CheckboxMulti
{
    public function getShadowTemplate(): string
    {
        return parent::getShadowTemplate()."<style>.checkboxmulti-checkbox-wrapper { display:flex; flex-direction: row; }</style>";
    }

    public function mergeShadowAttributes(): array
    {
        $attributes = [];
        $initJs = <<<JS
let values = JSON.parse(this.getAttribute('options'));
console.log(values);
let checkedValueStates = {};
let valueKeys = Object.keys(values);
let checkBoxElementsByValue = {};
valueKeys.forEach((key) => {
    checkedValueStates[key]=false;
    let checkBoxWrapper = element.cloneNode(true);
    let checkBoxInput = checkBoxWrapper.querySelector('input');
    checkBoxInput.id = id+'-'+key;
    checkBoxInput.value = key;
    checkBoxInput.name = name+"[]";
    let checkBoxLabel = checkBoxWrapper.querySelector('label');
    checkBoxLabel.innerHTML = values[key];
    checkBoxLabel.setAttribute('for',id+'-'+key);
    element.parentNode.append(checkBoxWrapper);
    checkBoxElementsByValue[key] = checkBoxInput;
    checkBoxInput.addEventListener('input',(evt)=>{
        checkedValueStates[evt.target.value] = evt.target.checked;
        let exposeValue = [];
        Object.keys(checkedValueStates).forEach((key) => {
            if (checkedValueStates[key]) {
                exposeValue.push(key);
            }
        });
        this.internals_.setFormValue(JSON.stringify(exposeValue));
    })
});
element.style.display='none';
JS;
        $attributes['options'] = new Attribute(
            'options',
            '.control-container .checkboxmulti-checkbox-wrapper',
            Attribute::TYPE_JSON_ARRAY,
            $initJs,
            '',
        );

        $initJs = <<<JS
let data;
try {
    data = JSON.parse(this.getAttribute('value'));
}
catch(e) {
    data = [this.getAttribute('value')];
}
const checkboxElements = this.template.querySelectorAll('input');
let expectedValues = [];
let checkedValues = [];
const setFormData = (elements) => {
    let values = [];
    elements.forEach((element)=> {
        if (element.checked) {
            values.push(element.value);
        }
    });
    this.internals_.setFormValue(JSON.stringify(values));
}

checkboxElements.forEach((node, index) => {
    if (index>0) {
        const value = node.getAttribute('value');
        expectedValues.push(node.value);
        if (value && data.includes(node.getAttribute('value'))) {
            checkedValues.push(node.value);
            node.checked = true;
        }
        else {
            node.checked = false;
        }
        node.addEventListener('change',()=>{setFormData(checkboxElements);});
    }
    else {
        node.style.display = 'none';
    }
});
setFormData(checkboxElements);
JS;
        $attributes['value'] = new Attribute(
            'value',
            '.control-container .checkboxmulti-checkbox-wrapper',
            Attribute::TYPE_ARRAY,
            $initJs,
            ''
        );

        return $attributes;
    }
//
//    /**
//     * @return string[]
//     */
//    public function shadowJavascript(): array
//    {
//        return[
//               /* promote the hidden input to the form and remove from the shadowdom */
//                '.component-container input[type=hidden]' => <<<JS
//element.name=(namespaceAttr ? namespaceAttr+'[expected_data][]' : 'expected_data');
//element.value=nameAttr;
//this.internals_.form.appendChild(element.cloneNode(false));
//element.parentElement.removeChild(element);
//JS
//            ]  + parent::shadowJavascript();
//    }
}
