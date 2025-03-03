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
     * @var Attribute[] $attributes
     */
    private array $attributes;

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
        $this->attributes = $this->component->getShadowAttributes();
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
registerDeformComponent('$componentClass', '$componentName', $classJavascript);
JS;
    }

    private function generateJavascriptClass(string $componentName, string $componentClass): string
    {
        $propertyDeclarations = '';
        $constructor=$this->generateConstructor($componentName);
        $connectedCallback=$this->generateConnectedCallback($componentName);
        $getters=$this->generateGetters($componentName);
        $dynamicCallbacks=$this->getAttributeChangedCallbackRules();
        $classJs = <<<JS
class $componentClass extends HTMLElement {
    static formAssociated = true;
    template = null;
    container = null;
    form = null;
$propertyDeclarations
$constructor
$getters
$connectedCallback
$dynamicCallbacks
}
JS;
        return $classJs;
    }

    private function generateConstructor(string $componentName): string
    {
        $shadowTemplate = $this->component->getShadowTemplate();
        $containerDefinition = $this->component->componentContainer->controlOnly
            ? "this.template"
            : "this.template.querySelector('#namespace-name-container')";
        $constructorJs = <<<JS
constructor() {
    super();
    this.internals_ = this.attachInternals();
    this.template = document.createElement('div');
    this.template.id='$componentName';
    this.template.innerHTML = `$shadowTemplate`;
    this.container = $containerDefinition;
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

        $attributeSetupJs = <<<JS
this.form = this.closest('form');
let namespaceAttr = null;
if (this.hasAttribute('namespace')) {
    namespaceAttr=this.getAttribute('namespace').toLowerCase();
    if (namespaceAttr.toLowerCase()==='none') {
        namespaceAttr = null;
    }
}
else {
    if (this.form!==null && this.form.hasAttribute('data-namespace')) {
        namespaceAttr = this.form.getAttribute('data-namespace');
    }
}
let nameAttr = this.getAttribute('name');
let idAttr = this.hasAttribute('id') ? this.getAttribute('id') : nameAttr;
let name = namespaceAttr ? namespaceAttr+"["+nameAttr+"]" : nameAttr;
let id = idAttr ? idAttr : 'deform-button-'+ (namespaceAttr?namespaceAttr+'-':'')+nameAttr;
this.setAttribute('name',name);

JS;

        $attributeSetupJs = Strings::prependPerLine($attributeSetupJs,"    ");

        if ($this->component->componentContainer->controlOnly) {
            $generatedComponentRulesJs = $this->getConnectedCallbackRules();
            $containerSetupJs = <<<JS
this.container.firstElementChild.name = id;
let element;
$generatedComponentRulesJs
JS;
        }
        else {
            $generatedComponentRulesJs = $this->getConnectedCallbackRules();
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
let element;
$generatedComponentRulesJs
JS;
            $containerSetupJs = Strings::prependPerLine($containerSetupJs, "    ");
        }
        $ifContainerSetupJs = <<<JS
if (this.container!==null) {
$containerSetupJs    
}
JS;
        $ifContainerSetupJs = Strings::prependPerLine($ifContainerSetupJs, "    ");

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
    private function getConnectedCallbackRules(): string
    {
        $generatedComponentRules = ["/* start : connected callback rules */"];

        foreach ($this->attributes as $attribute) {
            if ($attribute->selector!==Attribute::SLOT_SELECTOR) {
                $ifNotDynamic = $attribute->hideIfEmpty && $attribute->name!=='value'
                    ? "else { element.style.display = 'none' }"
                    : "";
                $generatedComponentRules[] = <<<JS
element = this.container.querySelector("{$attribute->selector}");
if (element!==null) { 
    if (this.hasAttribute('{$attribute->name}'))
    {
        {$attribute->initialiseJs}
    }
    $ifNotDynamic
}
else {
    console.error("Failed to find '{$this->componentName}' attribute '{$attribute->name}' element using selector '{$attribute->selector}'");
}
JS;
            }
            else if ($attribute->initialiseJs) {
                // full control over content!
                $generatedComponentRules[] = $attribute->initialiseJs;
            }
        }
        $generatedComponentRules[] = "/* end : connected callback rules */";
        return implode("\n", $generatedComponentRules);
    }

    public function generateGetters($componentName): string {
        $metadata = [];
        foreach ($this->attributes as $attribute) {
            $metadata[$attribute->name] = $attribute->metadata();
        }
        $metadata = json_encode($metadata);
        $js = <<<JS
    static get metadata() {
        return $metadata;
    }
    static get name() {
        return '$componentName';
    }
    
JS;
        return $js;
    }

    public function getAttributeChangedCallbackRules(): string {
        $observed = [];
        $callbacks = ['/* start : attribute changed callback rules */'];
        foreach ($this->attributes as $attribute) {
            if ($attribute->dynamic) {
                $observed[] = $attribute->name;
            }
            $callback = <<<JS
if (name==='{$attribute->name}' && this.shadowRoot) {
    const element = this.container.querySelector("{$attribute->selector}");
    if (element) {
        //console.log('set dynamic : {$attribute->name}');
        if (newValue) {
            element.style.display='block';
        }
        else {
            element.style.display='none';
        }
        {$attribute->updateJs}
    }
}
JS;
            $callbacks[] = $callback;
        }
        $callbacks[] = '/* end : attribute changed callback rules */';
        $observedArray = "['".implode("','", $observed)."']";

        $callbacksJs = implode("\n", $callbacks);
        $callbacksJs = Strings::prependPerLine($callbacksJs,"    ");
        $js = <<<JS
static get observedAttributes() {
    return $observedArray;
}
    
attributeChangedCallback(name, oldValue, newValue) {
$callbacksJs
}
JS;
        return Strings::prependPerLine($js,'    ');
    }
}