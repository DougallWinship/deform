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
setOptions(element, initialise) 
{
    let options = Deform.parseJson(this.getAttribute('options'), "Failed to parse CheckboxMulti 'options'");
    if (options===null) {
        options = [];
    }
    if (initialise) {
        /* ensure repeatable template is hidden */
        element.style.display = 'none';
    }

    /* always add and remove options! */
    Array.from(element.parentNode.children).forEach((child, index) => {
        if (index>0) {
            child.remove();
        }
    });

    options.forEach((keyValue) => {
        const key = keyValue[0];
        const value = keyValue[1];
        const id = "input-checkbox-"+key;
        const checkBoxWrapper = element.cloneNode(true);
        checkBoxWrapper.style.setProperty('display', 'flex');
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
            let value = JSON.stringify(exposeValue);
            this.setAttribute('value', value);
            this.internals_.setFormValue(value);
            this.emitEvent("change", value);
        });
    });
    if (!this.hasAttribute('value')) {
        this.internals_.setFormValue('[]');
        this.emitEvent("change", '[]');
    }
}
setFormData(checkboxElements) 
{
    let values = [];
    checkboxElements.forEach((element)=> {
        if (element.checked) {
            values.push(element.value);
        }
    });
    const value = JSON.stringify(values);
    this.internals_.setFormValue(value);
    this.emitEvent('change', value);
}
initValue(jsonValue) 
{
    const values = Deform.parseJson(jsonValue, "Failed to parse CheckboxMulti 'value'");
    const checkboxElements = this.template.querySelectorAll('input');
    checkboxElements.forEach((node, index) => {
        if (index>0) {
            const value = node.getAttribute('value');
            if (value && values.includes(node.getAttribute('value'))) {
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
updateValue(value) 
{
    if (value===null) return;
    const values = Deform.parseJson(value, "Failed to parse CheckboxMulti 'value'");
    if (values===null) {
        return;
    }
    const checkboxElements = this.template.querySelectorAll('input');
    checkboxElements.forEach((checkbox) => {
        if (values.includes(checkbox.getAttribute('value'))) {
            checkbox.checked = true;
        }
        else {
            checkbox.checked = false;
        }
    });
    this.internals_.setFormValue(JSON.stringify(values));
}
JS;
    }

    public function mergeShadowAttributes(&$attributes): void
    {
        $attributes['options'] = new Attribute(
            'options',
            '.control-container .checkboxmulti-checkbox-wrapper',
            Attribute::TYPE_KEYVALUE_ARRAY,
            "this.setOptions(element,true);",
            "this.setOptions(element,false);",
            Attribute::BEHAVIOUR_CUSTOM
        );
        $attributes['value'] = new Attribute(
            'value',
            '.control-container .checkboxmulti-checkbox-wrapper',
            Attribute::TYPE_ARRAY,
            "this.initValue(this.getAttribute('value'));",
            "this.updateValue(newValue);",
            default: '[]'
        );
    }
}
