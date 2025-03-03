<?php

declare(strict_types=1);

namespace Deform\Component\Shadow;

trait RadioButtonSet
{
    public function mergeShadowAttributes(): array
    {
        $attributes = [];

        $initJs = <<<JS
let values = JSON.parse(this.getAttribute('options'));
Object.keys(values).forEach((key) => {
    let radiobuttonWrapper = element.cloneNode(true);
    let radiobuttonInput = radiobuttonWrapper.querySelector('input');
    radiobuttonInput.id = 'radiobutton-'+key;
    radiobuttonInput.value = key;
    radiobuttonInput.name = name+"[]";
    radiobuttonInput.style.display = 'inline-block';
    let radiobuttonLabel = radiobuttonWrapper.querySelector('label');
    radiobuttonLabel.innerHTML = values[key];
    radiobuttonLabel.setAttribute('for','radiobutton-'+key);
    radiobuttonLabel.style.display = 'inline-block';
    element.parentNode.append(radiobuttonWrapper);
});
JS;
        $optionsJs = <<<JS
if (oldValue && newValue!==oldValue) {
    let values;
    try {
        values = JSON.parse(newValue);
    }
    catch (err) {
        console.error("invalid options json : "+newValue);
        return;
    }
    element.parentElement.querySelectorAll('.radiobuttonset-radio-container').forEach((radiobutton, index) => {
        if (index>0) {
            radiobutton.remove();
        }
        else {
            radiobutton.style.display="none";
        }
    })
   Object.keys(values).forEach((key) => {
        let radiobuttonWrapper = element.cloneNode(true);
        let radiobuttonInput = radiobuttonWrapper.querySelector('input');
        radiobuttonInput.id = 'radiobutton-'+key;
        radiobuttonInput.value = key;
        radiobuttonInput.name = name+"[]";
        radiobuttonInput.style.display = 'inline-block';
        let radiobuttonLabel = radiobuttonWrapper.querySelector('label');
        radiobuttonLabel.innerHTML = values[key];
        radiobuttonLabel.setAttribute('for','radiobutton-'+key);
        radiobuttonLabel.style.display = 'inline-block';
        radiobuttonWrapper.style.display = 'block';
        element.parentNode.append(radiobuttonWrapper);
    });
    
}
JS;

        $attributes['options'] = new Attribute(
            'options',
            '.control-container .radiobuttonset-radio-container',
            Attribute::TYPE_JSON_ARRAY,
            $initJs,
            $optionsJs,
        );

        $initJs = <<<JS
let checked = this.getAttribute('value');
let checkboxElements = this.template.querySelectorAll(".component-container input");
/* let expectedValues = []; */
let checkedValue = null;
checkboxElements.forEach((node, index) => {
    if (index>0) {
        const value = node.getAttribute('value');
        if (checked===value) {
            checkedValue=value;
            node.checked = true;
        }
        else {
            node.checked = false;
        }
        node.addEventListener('change', () => {
            this.internals_.setFormValue(node.value);
        })
    }
    else {
        node.parentNode.style.display = 'none';
    }
});
this.internals_.setFormValue(checkedValue);
JS;
        $attributes['value'] = new Attribute(
            'value',
            '.control-container .radiobuttonset-radio-container',
            Attribute::TYPE_STRING,
            $initJs,
            ''
        );

        return $attributes;
    }
}