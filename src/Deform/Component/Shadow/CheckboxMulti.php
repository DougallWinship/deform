<?php

declare(strict_types=1);

namespace Deform\Component\Shadow;

trait CheckboxMulti
{
    public function getShadowTemplate(): string
    {
        return parent::getShadowTemplate() .
            "<style>.checkboxmulti-checkbox-wrapper { display:flex; flex-direction: row; }</style>";
    }

    public function getShadowMethods(): string
    {
        return <<<JS
setOptions(element, clearFirst=false) 
{
    let options = Deform.parseJson(this.getAttribute('options'), "Failed to parse CheckboxMulti 'options'");
    if (options===null) {
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
        checkBoxInput.name = checkBoxInput.name+"[]";
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
            this.setAttribute('value', JSON.stringify(exposeValue));
            this.internals_.setFormValue(JSON.stringify(exposeValue));
        });
    });
    if (!this.hasAttribute('value')) {
        this.internals_.setFormValue('[]');
    }
}
setFormData(optionElements) 
{
    let values = [];
    optionElements.forEach((element)=> {
        if (element.checked) {
            values.push(element.value);
        }
    });
    this.internals_.setFormValue(JSON.stringify(values));
}
setupControls(values) 
{
    if (values===null) return;
    const checkboxElements = this.template.querySelectorAll('input');
    let expectedValues = [];
    let checkedValues = [];
    checkboxElements.forEach((node, index) => {
        if (index>0) {
            const value = node.getAttribute('value');
            expectedValues.push(node.value);
            if (value && values.includes(node.getAttribute('value'))) {
                checkedValues.push(node.value);
                node.checked = true;
            }
            else {
                node.checked = false;
            }
            node.addEventListener('change',() => {
                this.setFormData(checkboxElements);
            });
        }
    });
    this.setFormData(checkboxElements);
}
updateControls(values) 
{
    if (values===null) return;
    const checkboxElements = this.template.querySelectorAll('input');
    checkboxElements.forEach((checkbox) => {
        if (values.includes(checkbox.getAttribute('value'))) {
            checkbox.checked = true;
        }
        else {
            checkbox.checked = false;
        }
    })
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
            "this.setOptions(element,false); element.style.display='none';",
            "this.setOptions(element,true);"
        );
        $attributes['value'] = new Attribute(
            'value',
            '.control-container .checkboxmulti-checkbox-wrapper',
            Attribute::TYPE_ARRAY,
            "this.setupControls(Deform.parseJson(this.getAttribute('value'), 'Failed to parse CheckboxMulti \'value\''))",
            "this.updateControls(Deform.parseJson(newValue, 'Failed to parse CheckboxMulti \'value\''));"
        );
        return $attributes;
    }
}
