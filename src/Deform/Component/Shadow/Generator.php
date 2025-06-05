<?php

declare(strict_types=1);

namespace Deform\Component\Shadow;

use Deform\Component\BaseComponent;
use Deform\Component\ComponentFactory;
use Deform\Exception\DeformComponentException;
use Deform\Exception\DeformException;
use Deform\Util\Strings;
use Deform\Version;

/**
 * generate javascript for custom HTML elements representing the components
 *
 * Notes:
 * - the PHP component is used to generate the template for the custom component
 * - the template is automatically decorated with 'part' attributes (based on id, class & tag) for light dom styling
 * - when a PHP component has defined a template method using the @templateMethod annotation, it is processed like this
 *   via the prepareTemplateMethods below (lines 63-86):
 *     - if a single param is passed to the templateMethod then it is invoked with the value {item} which can then be
 *       used to replace the item value in the shadow dom (currently Currency is the only example where is it used to
 *       replace the currency symbol)
 *     - if an array is passed to the templateMethod then if is again invoked but with only a single array value
 *       ```[ '{repeatable-value}' => '{repeatable-value-label}' ]```, this is used to regenerate an arrow of items in
 *       the shadow dom by treating this item as a template for cloning, for example for SelectMulti the following is
 *       generated in the shadow dom, then subsequently hidden but used for cloning multiple values
 * phpcs:disable
 * ```php
 *          <div class='checkboxmulti-checkbox-wrapper' part='deform-checkboxmulti-checkbox-wrapper'>
 *              <input type='checkbox' id='checkboxmulti-namespace-name-{repeatable-value}' name='namespace[name][]' value='{repeatable-value}' part='deform-input deform-input-checkbox'>
 *              <label for='checkboxmulti-namespace-name-{repeatable-value}' class='multi-label' part='deform-multi-label'>{repeatable-value-label}</label>
 *          </div>
 * ```
 * phpcs:enable
 */
class Generator
{
    private string $componentName;

    private BaseComponent $component;

    /**
     * @var Attribute[] $attributes
     */
    private array $attributes;
    
    public static function alterDeformObject()
    {
        list($short, $full) = Version::getGitVersions();
        return <<<JS
if (window.Deform !== undefined) {
    window.Deform.version = '{$short}';
    window.Deform.fullVersion = '{$full}';
}
JS;
    }

    /**
     * @param string $componentName
     * @throws DeformException
     */
    public function __construct(string $componentName)
    {
        $this->componentName = $componentName;
        $this->component = ComponentFactory::build($componentName, 'namespace', "name")
            ->label("{label}", true)
            ->hint("{hint}")
            ->setError('{error}');
        $this->attributes = $this->component->getShadowAttributes();
        $this->prepareTemplateMethods();
    }

    private function prepareTemplateMethods(): void
    {
        $templateMethods = $this->component->getTemplateMethods();
        if (count($templateMethods) > 0) {
            foreach ($templateMethods as $templateMethod) {
                $params = $templateMethod->getParameters();
                if (count($params) > 1) {
                    throw new DeformComponentException("Not yet supported!");
                } elseif (count($params) === 1) {
                    $type = $params[0]->getType();
                    $typeName = $type->getName();
                    if ($typeName === 'array') {
                        $templateMethod->invoke($this->component, ['{repeatable-value}' => '{repeatable-value-label}']);
                    } elseif ($typeName === 'string') {
                        $templateMethod->invoke($this->component, '{item}');
                    } else {
                        throw new DeformComponentException("As yet unsupported @templateMethod parameter type " .
                            "'" . $typeName . "' for " . $templateMethod->name);
                    }
                }
            }
        }
    }

    public function generateCustomComponentJavascript(): string
    {
        $componentName = "deform-" . \Deform\Util\Strings::separateCased($this->componentName, "-");
        $componentClass = "DeformComponent" . $this->componentName;
        $classJavascript = $this->generateJavascriptClass($componentName, $componentClass);
        return <<<JS
Deform.registerComponent('$componentClass', '$componentName', $classJavascript);
JS;
    }

    private function generateJavascriptClass(string $componentName, string $componentClass): string
    {
        $additionalAttributes = $this->additionalAttributes();
        if ($additionalAttributes !== null) {
            $additionalAttributes = Strings::prependPerLine($additionalAttributes, "    ");
        }
        $constructor = Strings::prependPerLine($this->generateConstructor($componentName), "    ");
        $shadowMethods = $this->component->getShadowMethods();
        if ($shadowMethods) {
            $shadowMethods = Strings::prependPerLine($shadowMethods, "    ");
        }
        $connectedCallback = Strings::prependPerLine($this->generateConnectedCallback($componentName), "    ");
        $additionalMethods = Strings::prependPerLine($this->getAdditionalMethods($componentName), "    ");
        $dynamicCallbacks = Strings::prependPerLine($this->getAttributeChangedCallbackRules(), "    ");
        $classJs = <<<JS
class $componentClass extends HTMLElement {
    static formAssociated = true;
    template = null;
    container = null;
    form = null;
    isConnected = false;
    namespace = null;
    namespaceChecked = false;
    baseName = null;
    hasInvalidName = false;
    metadata = null;
    syncGuards = {};
$additionalAttributes
$constructor
$shadowMethods
$additionalMethods
$connectedCallback
$dynamicCallbacks
    setComponentName(componentFullName) {
        if (!Deform.isValidName(componentFullName)) {
            this.hasInvalidName = true;
            this.baseName = null;
            return false;
        }
        else {
            this.namespace = Deform.extractNamespace(componentFullName);
            this.baseName = Deform.extractBaseName(componentFullName);
            this.hasInvalidName = false;
            return true;
        }
    }
    setComponentBaseName(componentBaseName) {
        if (!Deform.isValidBaseName(componentBaseName)) {
            this.hasInvalidName = true;
            this.baseName = null;
            return false;
        }
        else {
            this.baseName = componentBaseName;
            this.hasInvalidName = false;
            return true;
        }
    }
    setComponentNamespace(componentNamespace) {
        if (!Deform.isValidNamespace(componentNamespace)) {
            this.hasInvalidName = true;
            this.namespace = componentNamespace;
            return false;
        }
        else {
            this.namespace = componentNamespace;
            return true;
        }
    }
    getComponentNamespace() {
        return this.namespace;
    }
    getComponentBaseName() {
        return this.baseName;
    } 
    triggerNameUpdated() {
        this.setAttribute('name', this.namespace+"["+this.baseName+"]");
    }
    isGuarded(field) {
        return this.syncGuards[field];
    }
    guard(field) {
        this.syncGuards[field]=true;
    }
    unguard(field) {
        this.syncGuards[field]=false;
    }
}
JS;
        return $classJs;
    }

    private function additionalAttributes(): string
    {
        $additionalAttributes = $this->component->getAdditionalAttributes();
        if (!$additionalAttributes) {
            return "";
        }
        return implode(";" . PHP_EOL, $additionalAttributes) . ";";
    }

    private function generateConstructor(string $componentName): string
    {
        $shadowTemplate = $this->component->getShadowTemplate();
        $containerDefinition = $this->component->componentContainer->controlOnly
            ? "this.template"
            : "this.template.querySelector('#namespace-name-container')";
        return <<<JS
constructor() {
    super();
    this.internals_ = this.attachInternals();
    this.template = document.createElement('div');
    this.template.id='$componentName';
    this.template.innerHTML = `$shadowTemplate`;
    this.container = $containerDefinition;
    this.isConnected = false;
    this.namespaceChecked = false;
    this.metadata = this.constructor.metadata;
    const shadowRoot = this.attachShadow({mode:'open', delegatesFocus: true});
    shadowRoot.appendChild(this.template);
}
JS;
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
if (!this.componentName) {
    this.setComponentName(this.getAttribute('name'));
    const componentNameObserver = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (mutation.type==='attributes' && mutation.attributeName==='name') {
                this.setComponentName(mutation.target.getAttribute('name'));
            }
        })
    });
    componentNameObserver.observe(this, {
        attributes: true,
        attributeFilter: ['name']
    });
}

if (this.namespaceChecked === false) {
    this.form = this.closest('form');
    if (this.form) {
        this.namespace = this.form.dataset.namespace;
        if (this.namespace) {
            this.setAttribute('name', this.namespace+"["+this.getAttribute('name')+"]");
            const formObserver = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.type==='attributes' && mutation.attributeName==="data-namespace") {
                        const newValue = mutation.target.getAttribute('data-namespace');
                        this.setComponentNamespace(newValue);
                        this.triggerNameUpdated();
                    }
                });
            });
            formObserver.observe(this.form, {
                attributes: true,
                attributeFilter: ['data-namespace']
            });
        }
    }
    else {
        console.warn("this element has no parent form!");
    }
    this.namespaceChecked = true;
}

const container = this.template.querySelector('.component-container');
if (container) {
    container.removeAttribute('id');
}
Object.keys(this.metadata).forEach((metadataKey) => {
    this.syncGuards[metadataKey] = false;
});
JS;
        $connectedCallbackSetup = Strings::prependPerLine($connectedCallbackSetup, "    ");
        $connectedCallbackRulesJs = Strings::prependPerLine($this->getConnectedCallbackRules(), "    ");
        return <<<JS
connectedCallback() {
$connectedCallbackSetup
$connectedCallbackRulesJs
    const metadata = this.constructor.metadata;
    Object.keys(metadata).forEach((key) => {
        const item = metadata[key];
        if (!this.hasAttribute(item.name) && item['default']!==null) {
            this.setAttribute(item.name, item['default']);
        }
    });
    this.isConnected=true;
}
JS;
    }

    /**
     * @return string
     * @throws DeformException
     */
    private function getConnectedCallbackRules(): string
    {
        $generatedComponentRules = [];

        foreach ($this->attributes as $attribute) {
            $failedToFind =
                "Failed to find '{$this->componentName}' attribute '{$attribute->name}' " .
                "using selector '{$attribute->selector}'";
            if ($attribute->selector === Attribute::SLOT_SELECTOR) {
                if ($attribute->initialiseJs) {
                    // full control over content!
                    $generatedComponentRules[] = $attribute->initialiseJs;
                }
            } elseif ($attribute->behaviour === Attribute::BEHAVIOUR_CUSTOM) {
                $generatedComponentRules[] = <<<JS
(()=> {
    const element = this.container.querySelector("{$attribute->selector}");
    if (element!==null) {
        {$attribute->initialiseJs}
    }
    else {
        console.error("{$failedToFind}");
    }
})();
JS;
            } elseif ($attribute->behaviour === Attribute::BEHAVIOUR_HIDE_IF_EMPTY) {
                $generatedComponentRules[] = <<<JS
(()=> {
    const element = this.container.querySelector("{$attribute->selector}");
    if (element!==null) { 
        if (this.hasAttribute('{$attribute->name}'))
        {
            {$attribute->initialiseJs}
            element.style.display="block";
        }
        else {
                element.style.setProperty("display","none","important");
        }
    }
    else {
        console.error("{$failedToFind}");
    }
})();
JS;
            } elseif ($attribute->behaviour === Attribute::BEHAVIOUR_VISIBLE_IF_EMPTY) {
                $generatedComponentRules[] = <<<JS
(() => {
    const element = this.container.querySelector("{$attribute->selector}");
    if (element!==null) { 
        if (this.hasAttribute('{$attribute->name}'))
        {
            {$attribute->initialiseJs}
            element.style.display="block";
        }
        else {
            console.log("Failed to find attribute {$attribute->name}!!");
        }
    }
    else {
        console.error("{$failedToFind}");
    }
})();
JS;
            } else {
                throw new DeformComponentException("Invalid behaviour : " . $attribute->behaviour);
            }
        }

        $js = Strings::prependPerLine(implode("\n", $generatedComponentRules), "    ");

        return <<<JS
/* start : connected callback rules */
requestAnimationFrame(() => {
$js
});
JS;
    }

    public function getAdditionalMethods($componentName): string
    {
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
setExpectedField(name, previousName=null) {
    if (!this.form) {
        return false;
    }
    let expectedValues = this.form.querySelector("input[name='expected_data']");
    if (!expectedValues) {
        expectedValues = document.createElement('input');
        expectedValues.type = 'hidden';
        expectedValues.name = 'expected-values';
        expectedValues.value = '[]';
        this.form.appendChild(expectedValues);
    }
    const jsonValues = expectedValues.value;
    let values = JSON.parse(jsonValues);
    if (previousName) {
        values = values.filter(item => item !== previousName);
    }
    values.push(name);
    expectedValues.value = JSON.stringify(values);
    return true;
}
JS;
        return $js;
    }

    public function getAttributeChangedCallbackRules(): string
    {
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
        if ('{$attribute->name}'!=='value' && '{$attribute->name}'!=='name') {
            if (newValue) {
                element.style.display="block"; 
            }
            else {
                element.style.setProperty("display","none","important");
            }
        }
        {$attribute->updateJs}
    }
}
JS;
            $callbacks[] = $callback;
        }
        $callbacks[] = '/* end : attribute changed callback rules */';
        $observedArray = "['" . implode("','", $observed) . "']";

        $callbacksJs = implode("\n", $callbacks);
        $callbacksJs = Strings::prependPerLine($callbacksJs, "    ");
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
        return $js;
    }
}
