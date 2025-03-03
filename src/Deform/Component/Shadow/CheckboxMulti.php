<?php

declare(strict_types=1);

namespace Deform\Component\Shadow;

trait CheckboxMulti
{
    public function getShadowTemplate(): string
    {
        return parent::getShadowTemplate()."<style>.checkboxmulti-checkbox-wrapper { display:flex; flex-direction: row; }</style>";
    }

    public function getShadowMethods(): string {
        return <<<JS
setOptions(element, clearFirst=false) 
{
    let options;
    try {
        options = JSON.parse(this.getAttribute('options'));
    }
    catch(e) {
        console.log("Failed to parse CheckboxMulti 'options'", e);
        return null;
    }
    if (clearFirst) {
        Array.from(element.parentNode.children).forEach((child, index) => {
            if (index>0) {
                child.remove();
            }
        });
    }
    options.forEach((keyValue) => {
        const key = keyValue[0];
        const value = keyValue[1];
        const id = "input-checkbox-"+key;
        const checkBoxWrapper = element.cloneNode(true);
        checkBoxWrapper.style.display = "flex";
        const checkBoxInput = checkBoxWrapper.querySelector('input');
        checkBoxInput.id = id;
        checkBoxInput.value = key;
        checkBoxInput.name = name+"[]";
        const checkBoxLabel = checkBoxWrapper.querySelector('label');
        checkBoxLabel.innerHTML = value;
        checkBoxLabel.setAttribute('for',id);
        element.parentNode.append(checkBoxWrapper);
        checkBoxInput.addEventListener('input',()=>{
            let exposeValue = [];
            Array.from(element.parentNode.children).forEach((child, index) => {
                if (index>0) {
                    if (child.firstChild.checked) {
                        exposeValue.push(child.firstChild.value);
                    }
                }
            });
            console.log("CheckboxMulti expose :"+exposeValue);
            this.internals_.setFormValue(JSON.stringify(exposeValue));
        });
    });
    if (!this.hasAttribute('value')) {
        this.internals_.setFormValue('[]');
    }
}

setFormData(element) 
{
    let optionElements = element.querySelector("input")
    let values = [];
    optionElements.forEach((element, index)=> {
        if (element.checked) {
            values.push(element.value);
        }
    });
    this.internals_.setFormValue(JSON.stringify(values));
}
JS;
    }

    public function mergeShadowAttributes(): array
    {
        $attributes = [];
        $attributes['options'] = new Attribute(
            'options',
            '.control-container .checkboxmulti-checkbox-wrapper',
            Attribute::TYPE_KEYVALUE_ARRAY,
            "this.setOptions(element); element.style.display='none';",
            "this.setOptions(element, true);"
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
