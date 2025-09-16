if (window.Deform !== undefined) {
    throw new Error("Deform is already defined. Possible duplicate inclusion?");
}
window.Deform = {
    components: {},
    registerComponent(componentClassName, componentName, definition) {
        this.components[componentClassName] = definition;
        customElements.define(componentName, definition);
    },
    getComponent(componentClassName) {
        return this.components[componentClassName];
    },
    isValidNamespace(ns) {
        return /^[a-zA-Z0-9_-]+$/.test(ns);
    },
    isValidBaseName(name) {
        return /^[a-zA-Z0-9_-]+$/.test(name);
    },
    isValidName(name) {
        return /^[a-zA-Z0-9_-]+$/.test(name) || /^[a-zA-Z0-9_-]+\[[a-zA-Z0-9_-]+\]$/.test(name);
    },
    extractBaseName(namespacedName) {
        const match = namespacedName.match(/\[([^\]]+)\]$/);
        return match ? match[1] : null;
    },
    extractNamespace(namespacedName) {
        const match = namespacedName.match(/^([^\[\]]+)\[[^\[\]]+\]$/);
        return match ? match[1] : null;
    },
    isTruthy(value) {
        if (!value) return false;
        const falsy = ["false","0","no","off"];
        return !falsy.includes(value);
    },
    parseJson(value, error)
    {
        try {
            return JSON.parse(value);
        }
        catch (err) {
            console.error(error,value);
            return null;
        }
    },
    ROUND:
    {
        STANDARD: 'standard',
        CEIL: 'ceil',
        FLOOR: 'floor',
        BANKERS: 'bankers',
    },
    round(value, type= Deform.ROUND.STANDARD, decimalPlaces=2) {
        switch (type) {
            case Deform.ROUND.STANDARD: return Deform.roundStandardTo(value, decimalPlaces);
            case Deform.ROUND.CEIL: return Deform.roundCeilTo(value, decimalPlaces);
            case Deform.ROUND.FLOOR: return Deform.roundFloorTo(value, decimalPlaces);
            case Deform.ROUND.BANKERS: return Deform.roundBankersTo(value, decimalPlaces);
            default:
                console.warn("Unsupported round type '" + type + "'");
                return Deform.roundStandardTo(value, decimalPlaces);
        }
    },
    roundStandardTo(value, decimalPlaces = 2)
    {
        const factor = 10 ** decimalPlaces;
        const rounded = Math.round(value * factor) / factor;
        return rounded.toFixed(decimalPlaces);
    },
    roundCeilTo(value, decimalPlaces = 2) {
        const factor = 10 ** decimalPlaces;
        const rounded = Math.ceil(value * factor) / factor;
        return rounded.toFixed(decimalPlaces);
    },
    roundFloorTo(value, decimalPlaces = 2) {
        const factor = 10 ** decimalPlaces;
        const rounded = Math.floor(value * factor) / factor;
        return rounded.toFixed(decimalPlaces);
    },
    roundBankersTo(value, decimalPlaces = 2) {
        const factor = Math.pow(10, decimalPlaces);
        const scaled = value * factor;
        const integer = Math.floor(scaled);
        const fraction = Math.abs(scaled - integer);

        let rounded;
        if (fraction !== 0.5) {
            rounded = Math.round(scaled) / factor;
        }
        else {
            // Handle .5 case — round to nearest even
            if (integer % 2 === 0) {
                rounded = integer / factor;
            } else {
                rounded = (value > 0 ? integer + 1 : integer - 1) / factor;
            }
        }
        return rounded.toFixed(decimalPlaces);
    },
    debounce(func, wait, immediate) {
        let timeout
        return function (...args) {
            const context = this
            const later = function () {
                timeout = null
                if (!immediate) func.apply(context, args)
            }
            const callNow = immediate && !timeout
            clearTimeout(timeout)
            timeout = setTimeout(later, wait)
            if (callNow) func.apply(context, args)
        }
    }
};

class DeformBase extends HTMLElement {

    static formAssociated = true;
    componentName = null;
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
    listeners = [];

    constructor() {
        super();
        this.internals_ = this.attachInternals();
        this.template = document.createElement('div');
    }
    connectedCallbackSetup() {
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
                console.warn(this.getAttribute('name') + " has no parent form!");
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
    }

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
            this.componentName = componentFullName;
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

    getComponentFullName() {
        return this.namespace + "[" + this.baseName + "]";
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

    emitEvent(type, value) {
        this.dispatchEvent(new CustomEvent(`deform:${type}`, {
            detail: { value },
            bubbles: true,
            composed: true
        }));
    }

    addArrowListener(element, type, handler) {
        if (handler.hasOwnProperty('prototype')) {
            throw new Error("addArrowListener expects an arrow function — got a non-arrow function for '" + type + "' on "+(typeof element));
        }
        this.listeners.push({ element, type, handler });
        element.addEventListener(type, handler);
    }

    disconnectedCallback() {
        this.listeners.forEach(({ element, type, handler }) => {
            element.removeEventListener(type, handler);
        });
        this.listeners = [];
        this.isConnected = false;
    }
}