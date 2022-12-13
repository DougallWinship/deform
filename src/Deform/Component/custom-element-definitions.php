<?php
/**
 * @var $componentNames
 */
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
            container = null;
            <?php foreach ($component->shadowJavascriptProperties() as $property=>$selector) { ?>
            <?= $property ?>=null;
            <?php } ?>
            constructor() {
                super();
                this.template = document.createElement('div');
                this.template.id='<?= $snakeName ;?>';
                this.template.innerHTML = `<?= $component->getShadowTemplate() ?>`;
                this.container = <?= $component->componentContainer->controlOnly ? "this.template;" : "this.template.querySelector('#namespace-name-container');" ?>

                <?php foreach ($component->shadowJavascriptProperties() as $property=>$selector) { ?>
                this.<?= $property ?>=<?= $selector ?>;
                <?php } ?>
                const shadowRoot = this.attachShadow({mode:'open'});
                shadowRoot.appendChild(this.template)
            }

            connectedCallback() {
                if (!this.hasAttribute('name')) {
                    console.error('"<?= $snakeName ?>" is missing the required attribute \'name\'');
                    this.container.innerHTML = "<div style='color:red'>'<?= $snakeName ?>' is missing the required attribute 'name'</div>";
                    return
                }
                let namespaceAttr = null;
                if (this.hasAttribute('namespace')) {
                    namespaceAttr=this.getAttribute('namespace').toLowerCase();
                    if (namespaceAttr.toLowerCase()==='none') {
                        namespaceAttr = null;
                    }
                }
                else {
                    let parentForm = this.closest('form');
                    if (parentForm!==null && parentForm.hasAttribute('data-namespace')) {
                        namespaceAttr = parentForm.getAttribute('data-namespace');
                    }
                }

                let nameAttr = this.getAttribute('name')
                let idAttr = this.hasAttribute('id') ? this.getAttribute('id') : nameAttr;
                let name = namespaceAttr ? namespaceAttr+"["+nameAttr+"]" : nameAttr;
                let id = idAttr ? idAttr : '<?= $snakeName ?>-'+ (namespaceAttr?namespaceAttr+'-':'')+nameAttr;

                if (this.container!==null) {
                    <?php if ($component->componentContainer->controlOnly) { ?>
                    this.container.firstElementChild.name = id
                    <?php } else { ?>
                    this.container.removeAttribute('id');
                    let controlContainer = this.container.querySelector('.control-container');
                    if (controlContainer!==null) {
                        if (controlContainer.children.length>1) {
                            for (let idx=0; idx<controlContainer.children.length; idx++) {
                                controlContainer.children[idx].id = id + "-" + idx
                                controlContainer.children[idx].name=name+"[]"
                            }
                        }
                        else if (controlContainer.children.length===1) {
                            controlContainer.id = id
                            controlContainer.firstElementChild.name = name
                        }
                    }
                    <?php } ?>


                    /* start : generated component rules */
                    let element
                    <?php foreach ($component->getShadowJavascript() as $selector=>$javascript) { if ($javascript!==null) { ?>
                    element = this.container.querySelector('<?= $selector ?>');
                    if (element!==null) {<?php echo \Deform\Util\Strings::trimInternal($javascript); ?>}
                    <?php } } ?>
                    /* end : generated component rules */
                }
            }
        }
    )
<?php
}
?>