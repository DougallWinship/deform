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
        console.log('round type=' + type + ' dp='+decimalPlaces);
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
        return Math.round(value * factor) / factor;
    },
    roundCeilTo(value, decimalPlaces = 2) {
        const factor = 10 ** decimalPlaces;
        return Math.ceil(value * factor) / factor;
    },
    roundFloorTo(value, decimalPlaces = 2) {
        const factor = 10 ** decimalPlaces;
        return Math.floor(value * factor) / factor;
    },
    roundBankersTo(value, decimalPlaces = 2) {
        const factor = Math.pow(10, decimalPlaces);
        const scaled = value * factor;
        const integer = Math.floor(scaled);
        const fraction = Math.abs(scaled - integer);

        if (fraction !== 0.5) {
            return Math.round(scaled) / factor;
        }

        // Handle .5 case — round to nearest even
        if (integer % 2 === 0) {
            return integer / factor;
        } else {
            return (value > 0 ? integer + 1 : integer - 1) / factor;
        }
    }
};