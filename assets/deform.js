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