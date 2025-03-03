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
        $constructor = $this->generateConstructor($componentName);
        $shadowMethods = $this->component->getShadowMethods();
        if ($shadowMethods) {
            $shadowMethods = Strings::prependPerLine($shadowMethods,"    ");
        }
        $connectedCallback=$this->generateConnectedCallback($componentName);
        $additionalMethods=$this->getAdditionalMethods($componentName);
        $dynamicCallbacks=$this->getAttributeChangedCallbackRules();
        $classJs = <<<JS
class $componentClass extends HTMLElement {
    static formAssociated = true;
    template = null;
    container = null;
    form = null;
    namespace = null;
    isConnected = false;
$propertyDeclarations
$constructor
$shadowMethods
$additionalMethods
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
    this.isConnected = false;
    const shadowRoot = this.attachShadow({mode:'open'});
    shadowRoot.appendChild(this.template)
}
JS;
        return Strings::prependPerLine($constructorJs, "    ");
    }

    private function generateConnectedCallback($componentName): string
    {
        $connectedCallbackSetup = <<<JS
if (!this.hasAttribute('name')) {
    let errorMessage = "'$componentName' is missing the required attribute 'name'";
    console.error(errorMessage);
    this.container.innerHTML = "<div style='color:red'>"+errorMessage+"</div>";
    return;
}
this.form = this.closest('form');
if (this.form) {
    this.namespace = this.form.dataset.namespace;
    if (this.namespace) {
        this.setAttribute('name', this.namespace+"["+this.getAttribute('name')+"]");
    }
}
else {
    console.warn("this element has no parent form!");
}
const container = this.template.querySelector('.component-container');
if (container) {
    container.removeAttribute('id');
}
JS;
        $connectedCallbackSetup = Strings::prependPerLine($connectedCallbackSetup,"    ");
        $connectedCallbackRulesJs = Strings::prependPerLine($this->getConnectedCallbackRules(), "    ");
        return <<<JS
connectedCallback() {
$connectedCallbackSetup
$connectedCallbackRulesJs
    this.isConnected=true;
}
JS;
    }

    /**
     *
     * @return string
     */
    private function getConnectedCallbackRules(): string
    {
        $generatedComponentRules = [
            "/* start : connected callback rules */",
            "let element;"
        ];

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

    public function getAdditionalMethods($componentName): string {
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
    static get namespace() {
        return this.namespace;
    }
    setExpectedField(name) {
        if (!this.form) {
            return false;
        }
        let expectedValues = this.form.querySelector("input[name='expected_data']");
        if (!expectedValues) {
            expectedValues = document.createElement('input');
            expectedValues.type = 'hidden';
            expectedValues.name = 'expected-values';
            this.form.appendChild(expectedValues);
        }
        let jsonValues = document.createElement('input');
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
        if ('{$attribute->name}'!=='value') {
            if (newValue) {
                element.style.display='reset';
            }
            else {
                alert('hide');
                element.style.display='none';
            }
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
    if (!this.isConnected) {
        return;
    }
$callbacksJs
}
JS;
        return Strings::prependPerLine($js,'    ');
    }
}