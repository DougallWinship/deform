<?php

declare(strict_types=1);

namespace Deform\Component\Shadow;

use Deform\Component\BaseComponent;
use Deform\Component\ComponentFactory;
use Deform\Util\Strings;

/**
 * generate javascript for custom HTML elements representing the components
 */
class Generator
{
    private string $componentName;

    private BaseComponent $component;

    /**
     * @param string $componentName
     * @throws \ReflectionException|\Exception
     */
    public function __construct(string $componentName)
    {
        $this->componentName = $componentName;
        $this->component = ComponentFactory::build($componentName, 'namespace', "name")
            ->label("{label}")
            ->hint("{hint}")
            ->setError('{error}');
        $this->prepareTemplateMethods();
    }

    /**
     * @return void
     * @throws \ReflectionException
     */
    private function prepareTemplateMethods(): void {
        $templateMethods = $this->component->getTemplateMethods();
        if (count($templateMethods) > 0) {
            foreach ($templateMethods as $templateMethod) {
                $params = $templateMethod->getParameters();
                if (count($params) > 1) {
                    throw new \Exception("Not yet supported!");
                } elseif (count($params) === 1) {
                    $type = $params[0]->getType();
                    $typeName = $type->getName();
                    if ($typeName === 'array') {
                        $templateMethod->invoke($this->component, ['{repeatable-value}' => '{repeatable-value-label}']);
                    } elseif ($typeName === 'string') {
                        $templateMethod->invoke($this->component, '{item}');
                    } else {
                        throw new \Exception("As yet unsupported @templateMethod parameter type " .
                            "'" . $typeName . "' for " . $templateMethod->name);
                    }
                }
            }
        }
    }

    public function generateCustomComponentJavascript(): string
    {
        $componentName = "deform-" . \Deform\Util\Strings::separateCased($this->componentName, "-");
        $componentClass = "DeformComponent".$this->componentName;
        $classJavascript = $this->generateJavascriptClass($componentName, $componentClass);
        return <<<JS
registerDeformComponent('$componentName', $classJavascript);
JS;
    }

    private function generateJavascriptClass(string $componentName, string $componentClass): string
    {
        $propertyDeclarations = '';
        foreach (array_keys($this->component->shadowJavascriptProperties()) as $property) {
            $propertyDeclarations.= "    ".$property."=null;".PHP_EOL;
        }
        $constructor=$this->generateConstructor($componentName);
        $connectedCallback=$this->generateConnectedCallback($componentName);
        $metadataMethod=$this->generateMetadataMethod($componentName);
        $classJs = <<<JS
class $componentClass extends HTMLElement {
    static formAssociated = true;
    template = null;
    container = null;
$propertyDeclarations
$constructor
$connectedCallback
$metadataMethod 
}
JS;
        return Strings::prependPerLine($classJs, "    ");
    }

    private function generateConstructor(string $componentName): string
    {
        $shadowTemplate = $this->component->getShadowTemplate();
        $containerDefinition = $this->component->componentContainer->controlOnly
            ? "this.template"
            : "this.template.querySelector('#namespace-name-container')";
        $shadowProperties =  '';
        foreach ($this->component->shadowJavascriptProperties() as $property => $selector) {
            $shadowProperties .= "    this.".$property." = ".$selector.";".PHP_EOL;
        }
        $constructorJs = <<<JS
constructor() {
    super();
    this.internals_ = this.attachInternals();
    this.template = document.createElement('div');
    this.template.id='$componentName';
    this.template.innerHTML = `$shadowTemplate`;
    this.container = $containerDefinition;
$shadowProperties
    const shadowRoot = this.attachShadow({mode:'open'});
    shadowRoot.appendChild(this.template)
}
JS;
        return Strings::prependPerLine($constructorJs, "    ");
    }

    private function generateConnectedCallback($componentName): string
    {

        $missingNameErrorCheck = <<<JS
if (!this.hasAttribute('name')) {
    console.error('"$componentName is missing the required attribute \'name\'');
    let e = "<div style='color:red'>'$componentName' is missing the required attribute 'name'</div>";
    this.container.innerHTML = e;
    return;
}
JS;
        $missingNameErrorCheckJs = Strings::prependPerLine($missingNameErrorCheck,"    ");

        $attributeSetup = <<<JS
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
let nameAttr = this.getAttribute('name');
let idAttr = this.hasAttribute('id') ? this.getAttribute('id') : nameAttr;
let name = namespaceAttr ? namespaceAttr+"["+nameAttr+"]" : nameAttr;
let id = idAttr ? idAttr : 'deform-button-'+ (namespaceAttr?namespaceAttr+'-':'')+nameAttr;
this.setAttribute('name',name);

JS;
        $attributeSetupJs = Strings::prependPerLine($attributeSetup,"    ");

        if ($this->component->componentContainer->controlOnly) {
            $containerSetupJs = <<<JS
this.container.firstElementChild.name = id;
JS;
        }
        else {
            $generatedComponentRulesJs = $this->getGeneratedComponentRules();
            $containerSetupJs = <<<JS
this.container.removeAttribute('id');
let controlContainer = this.container.querySelector('.control-container');
if (controlContainer!==null) {
    if (controlContainer.children.length>1) {
        for (let idx=0; idx<controlContainer.children.length; idx++) {
            controlContainer.children[idx].id = id + "-" + idx;
            controlContainer.children[idx].name=name+"[]";
        }
    }
    else if (controlContainer.children.length===1) {
        controlContainer.id = id;
        controlContainer.firstElementChild.name = name;
    }
}
$generatedComponentRulesJs
JS;
            $containerSetupJs = Strings::prependPerLine($containerSetupJs, "    ");
        }
        $ifContainerSetup = <<<JS
if (this.container!==null) {
$containerSetupJs    
}
JS;
        $ifContainerSetupJs = Strings::prependPerLine($ifContainerSetup, "    ");


        $connectedCallback = <<<JS

connectedCallback() {
$missingNameErrorCheckJs
$attributeSetupJs
$ifContainerSetupJs
}
JS;
        return Strings::prependPerLine($connectedCallback, "    ");
    }

    /**
     *
     * @return string
     */
    private function getGeneratedComponentRules(): string
    {
        $generatedComponentRules = ["/* start : generated component rules */"];
        foreach ($this->component->getShadowJavascript() as $selector => $javascript) {
            if ($javascript !== null) {
                $trimmedJavascript = \Deform\Util\Strings::trimInternal($javascript);
                $generatedComponentRules[] = <<<JS
(()=>{
    let element = this.container.querySelector('$selector');
    if (element!==null) { $trimmedJavascript }
})();
JS;
            }
        }
        $generatedComponentRules[] = "/* end : generated component rules */";
        return implode(PHP_EOL, $generatedComponentRules);
    }

    public function generateMetadataMethod(): string {
        $metadata = json_encode($this->component->getShadowMetadata());
        $js = <<<JS
    static get metadata() {
        return JSON.parse($metadata);
    }
    
JS;
        return $js;
    }
}