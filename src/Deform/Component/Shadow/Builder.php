<?php
namespace Deform\Component\Shadow;

class Builder
{
    /**
     * @return string
     */
    public static function javascriptDefinition(): string
    {
        $builder = new Builder();
        return $builder->getJavascriptDefinition();
    }

    /**
     * @return string
     */
    public function getJavascriptDefinition(): string
    {
        $componentSelector = self::htmlComponentSelector();
        $formArea = self::htmlFormArea();
        $formInfo = self::htmlFormInfo();
        $styles = self::styles();
        $js = <<<JS
class FormBuilder extends HTMLElement {
    static formAssociated = true;
    template = null;
    container = null;
    componentSelector = null;
    formArea = null;
    formInfo = null;
    constructor() {
        super();
        this.internals_ = this.attachInternals();
    }
    connectedCallback() {
        this.template = document.createElement('div');
        this.template.className = 'form-builder';
        
        this.componentSelector = document.createElement('div');
        this.componentSelector.className = 'component-selector';
        this.componentSelector.innerHTML = `$componentSelector`;
        this.template.appendChild(this.componentSelector);
        
        this.formArea = document.createElement('div');
        this.formArea.className = 'form-area';
        this.formArea.innerHTML = `$formArea`;
        this.template.appendChild(this.formArea);
        
        this.formInfo = document.createElement('div');
        this.formInfo.className = 'form-info';
        this.formInfo.innerHTML = `$formInfo`;
        this.template.appendChild(this.formInfo);
        
        this.container = this.template;
        const shadowRoot = this.attachShadow({
            mode: 'open'
        });
        shadowRoot.appendChild(this.template);
        
        const styles = document.createElement("style");
        styles.textContent = `$styles`;
        shadowRoot.appendChild(styles);
    }
}
customElements.define("form-builder", FormBuilder);
JS;
        return $js;
    }

    public function htmlComponentSelector(): string
    {
        return <<<HTML
component-selector
HTML;
    }

    public function htmlFormArea(): string
    {
        return <<<HTML
form-area
HTML;
    }

    public function htmlFormInfo(): string
    {
        return <<<HTML
form-info
HTML;
    }

    public function styles(): string
    {
        $styles = <<<STYLES
.form-builder {
    display: flex;
    flex-direction: row;
}
.form-builder .component-selector {
    min-width: 25%;
    background-color:#ffcccc;
}
.form-builder .form-area {
    flex-grow: 1;
    background-color:#ccffcc;
}
.form-builder .form-info {
    min-width: 25%;
    background-color:#ccccff;
}
STYLES;
        return $styles;
    }
}