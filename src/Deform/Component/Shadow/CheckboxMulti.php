<?php

declare(strict_types=1);

namespace Deform\Component\Shadow;

trait CheckboxMulti
{
    /**
     * @return string[]
     */
    public function shadowJavascript(): array
    {
        return[
                '.control-container .checkboxmulti-checkbox-wrapper' => <<<JS
if (this.hasAttribute('value')) {
    let values = JSON.parse(this.getAttribute('value'));
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
    element.remove();
    if (this.hasAttribute('checked')) {
        let data;
        try {
            data = JSON.parse(this.getAttribute('checked'));
        }
        catch(e) {
            data = [this.getAttribute('checked')];
        }
        let initialValues = [];
        data.forEach((key) => {
            if (key in checkBoxElementsByValue) {
                checkBoxElementsByValue[key].checked = true;
                initialValues.push(key);
            }
        });
        this.internals_.setFormValue(JSON.stringify(initialValues));
    }
}
else {
    element.remove();
}

JS,
               /* promote the hidden input to the form and remove from the shadowdom */
                '.component-container input[type=hidden]' => <<<JS
element.name=(namespaceAttr ? namespaceAttr+'[expected_data][]' : 'expected_data');
element.value=nameAttr;
this.internals_.form.appendChild(element.cloneNode(false));
element.parentElement.removeChild(element);
JS
            ]  + parent::shadowJavascript();
    }
}
