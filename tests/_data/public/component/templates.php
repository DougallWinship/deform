<?php

use Deform\Html\Html;

$componentNames = \Deform\Component\ComponentFactory::getRegisteredComponents();
$customElements = [];
?>
<script>
<?php
foreach ($componentNames as $componentName) {
    $lowerName = strtolower($componentName);
    $component = \Deform\Component\ComponentFactory::build($componentName, 'namespace', "name")
        ->label("{label}")
        ->hint("{hint}")
        ->setError('{error}');
    $templateMethods = $component->getTemplateMethods();
    $callables = [];
    $callableRepeaters = [];
    if (count($templateMethods)>0) {
        foreach ($templateMethods as $method) {
            $params = $method->getParameters();
            if (count($params)>1) {
                throw new \Exception("Not yet supported!");
            }
            else if (count($params)===1) {
                $type = $params[0]->getType();
                $typeName = $type->getName();
                if ($typeName==='array') {
                    $method->invoke($component,['{repeatable-value}' => '{repeatable-value-label}']);
                    $callableRepeaters[] = $method;
                }
                elseif ($typeName==='string') {
                    $method->invoke($component,'{item}');
                }
                else {
                    throw new \Exception("As yet unsupported @templateMethod parameter type '".$typeName."' for ".$method->name);
                }
            }
        }
    }
    $snakeName = "component-".\Deform\Util\Strings::separateCased($componentName,"-");
    $componentName = "Component".$componentName;
    $customElements[]=$snakeName;
    $controls = $component->componentContainer->control->getControls();
?>
window.customElements.define('<?= $snakeName ?>',
    class <?= $componentName ?> extends HTMLElement {
        template = null;
        componentContainer = null;
        labelContainer = null;
        label = null;
        controlContainer = null;
        control = null;
        constructor() {
            super();
            console.log('------------------');
            this.template = document.createElement('div');
            this.template.id='<?= $snakeName ;?>';
            this.template.innerHTML = `<?= $component.'' ?>`;
            this.componentContainer = this.template.querySelector('#namespace-name-container');
            this.controlContainer = this.template.querySelector(".control-container");
            const shadowRoot = this.attachShadow({mode:'open'});
            shadowRoot.appendChild(this.template)
        }

        connectedCallback() {
            let name = null
            if (this.hasAttribute('namespace') && this.hasAttribute('id')) {
                name = "["+this.getAttribute('namespace')+"]"+this.getAttribute('id')
                if (this.componentContainer!==null) {
                    this.componentContainer.id = this.getAttribute('namespace')+"-"+this.getAttribute('id')+"-container";
                }
            }
            else if (this.hasAttribute('id')) {
                name = this.getAttribute('id');
                this.componentContainer.id = this.getAttribute('id')+"-container";
            }
            else {
                //this.controlContainer
            }
            //console.log(this.template);
        }
    }
)
<?php
}
?>
</script>


<?php foreach ($customElements as $elementName) { ?>
    <<?= $elementName; ?>></<?= $elementName; ?>><br><hr><br>
<?php } ?>


<?php /*
<script>

    window.customElements.define('component-text-test',
        class ComponentText extends HTMLElement {

            template = null;
            componentContainer = null;
            labelContainer = null;
            label = null;
            controlContainer = null;
            control = null;
            hintContainer = null;
            errorContainer = null;
            namespace = null;
            name = null;

            constructor() {
                super();
                this.template = document.createElement('div');
                this.template.id='component-text-test';
                this.template.innerHTML = `<div id="{namespace}-{name}-container" class="component-container container-type-text"><div class="label-container"><label style="margin-bottom:0" for="text-{namespace}-{name}">{label}</label></div><div class="control-container"><input id="text-{namespace}-{name}" name="{namespace}[{name}]" type="text"></div><div class='hint-container'>{hint}</div><div class='error-container'>{error}</div></div>`;

                this.componentContainer = this.template.firstChild;
                this.labelContainer = this.componentContainer.firstChild;
                this.label = this.labelContainer.firstChild;
                this.controlContainer = this.labelContainer.nextSibling;
                this.control = this.controlContainer.firstChild;
                this.hintContainer = this.controlContainer.nextSibling;
                this.errorContainer = this.hintContainer.nextSibling;

                const shadowRoot = this.attachShadow({mode:'open'});
                shadowRoot.appendChild(this.template)
            }

            connectedCallback() {

                console.log('connected callback');
                console.log(this.getAttributeNames());
                if (this.hasAttribute('namespace')) {
                    console.log('has namespace');
                    this.namespace = this.getAttribute('namespace');
                    this.componentContainer.setAttribute('id', this.componentContainer.getAttribute('id').replace('{namespace}', this.namespace));
                    this.label.setAttribute('for', this.label.getAttribute('for').replace('{namespace}', this.namespace));
                    this.control.setAttribute('id', this.control.getAttribute('id').replace('{namespace}', this.namespace));
                    this.control.setAttribute('name', this.control.getAttribute('name').replace('{namespace}', this.namespace));
                }
                if (this.hasAttribute('name')) {
                    this.name = this.getAttribute('name');
                    this.componentContainer.setAttribute('id', this.componentContainer.getAttribute('id').replace('{name}', this.name));
                    this.label.setAttribute('for', this.label.getAttribute('for').replace('{name}', this.name));
                    this.control.setAttribute('id', this.control.getAttribute('id').replace('{name}', this.name));
                    this.control.setAttribute('name', this.control.getAttribute('name').replace('{name}', this.name));
                }
                if (this.hasAttribute('label')) {
                    this.label.innerText = this.getAttribute('label')
                }
                if (this.hasAttribute('hint')) {
                    this.hintContainer.innerText = this.getAttribute('hint')
                }
                if (this.hasAttribute('error')) {
                    this.errorContainer.innerText = this.getAttribute('error')
                }

                if (this.hasAttribute('value')) {
                    this.control.setAttribute('value', this.getAttribute('value'));
                }
                this.getAttributeNames().filter((name) => name.endsWith('-class')).forEach((nameClass) => {
                    const checkForProperty = this.toCamel(nameClass.substring(0, nameClass.length-6));
                    if (checkForProperty in this) {
                        this.getAttribute(nameClass).split(' ').forEach((classItem) => {
                            this[checkForProperty].classList.add(classItem);
                        })
                    }
                })
            }

            toCamel(str) {
                return str.replace(/([-_][a-z])/ig, (gr) => {
                    return gr.toUpperCase()
                        .replace('-', '')
                        .replace('_', '');
                });
            }
        }
    )
</script>


<component-text-test namespace="namespace" name="name" label="label" error="error" hint="hint" value="55" ></component-text-test>

 */ ?>