if (window.DeformSandbox !== undefined) {
    throw new Error("DeformSandbox is already defined. Possible duplicate inclusion?");
}
window.DeformSandbox = {};

// document.addEventListener('deform:change', e => {
//     console.log('GLOBAL deform:change caught', e.detail);
// }, true);

DeformSandbox.SandboxApp = class {
    constructor(components, root, availableComponentsElement, formAreaElement, formInfoElement) {
        this.components = components;
        this.root = root;
        this.availableComponentsElement = availableComponentsElement;
        this.formAreaElement = formAreaElement;
        this.formInfoElement = formInfoElement;
        this.availableComponentsPanel = new DeformSandbox.AvailableComponentsPanel(this, availableComponentsElement);
        this.formAreaPanel = new DeformSandbox.FormAreaPanel(this, formAreaElement);
        this.formInfoPanel = new DeformSandbox.FormInfoPanel(this, formInfoElement);
        this.dragData = null;
        this.selectedComponent = null;
        this.selectedComponentObject = null;
        this.selectedComponentListeners = [];
        this.selectedComponentObservers = [];
    }

    clearSelectedComponent()
    {
        //console.log("clearSelectedComponent", this.selectedComponentListeners);
        this.selectedComponentListeners.forEach(({ element, listener }) => {
            element.removeEventListener("deform:change", listener);
        })
        this.selectedComponentListeners = [];
        this.selectedComponentObservers.forEach(observer => {
            observer.disconnect();
        });
        this.selectedComponentObservers = [];
        this.selectedComponent.parentElement.classList.remove('selected');
        this.selectedComponent = null;
        this.selectedComponentObject = null;
        this.formInfoPanel.clearDynamicInfo();
    }

    setSelectedComponent(component, definition)
    {
        if (this.selectedComponent === component) {
            return;
        }
        if (this.selectedComponent!==null)
        {
            this.clearSelectedComponent();
        }
        this.selectedComponent = component;
        this.selectedComponent.parentElement.classList.add('selected');
        this.selectedComponentObject = definition;
        this.formInfoPanel.updateSelectedComponent();
    }
};

DeformSandbox.AvailableComponentsPanel = class {
    constructor(app, availableComponentsElement) {
        this.app = app;
        this.availableComponentsElement = availableComponentsElement;
        this.availableComponentsElement.querySelectorAll('.component').forEach((elem) => {
            elem.addEventListener('dragstart', (evt) => {
                evt.dataTransfer.effectAllowed = 'copy';
                this.app.dragData = JSON.stringify({
                    source:'select',
                    component: evt.target.getAttribute('data-component')
                });
                evt.target.classList.add('dragging');
            });
            elem.addEventListener('dragend', event => {
                event.target.classList.remove('dragging');
            });
        });
    }
}

DeformSandbox.FormAreaPanel = class {
    constructor(app, formAreaElement) {
        this.app = app;
        this.formAreaElement = formAreaElement;
        this.placeholder = document.createElement('div');
        this.placeholder.className = 'drop-placeholder';

        const logFormDataButton = this.formAreaElement.querySelector('button#dump-button');
        if (logFormDataButton) {
            document.addEventListener("formdata", (event) => {
                const entries = event.formData.entries();
                console.clear();
                console.log("Form Data:");
                for (const[key, value] of entries) {
                    console.log(key, value);
                }
            });
            logFormDataButton.addEventListener('click', () => {
                const formElem = document.getElementById("form-area");
                new FormData(formElem);
            })
        }

        this.formAreaElement.addEventListener('dragover', event => {
            event.preventDefault();
            const data = JSON.parse(this.app.dragData);
            event.dataTransfer.dropEffect = (data.source === 'select') ? 'copy' : 'move';
            this.formAreaElement.classList.add('dragover');

            // Calculate insertion point and position the placeholder
            const afterElement = this.getDragAfterElement(this.formAreaElement, event.clientY);
            if (afterElement == null) {
                this.formAreaElement.appendChild(this.placeholder);
            } else {
                this.formAreaElement.insertBefore(this.placeholder, afterElement);
            }
        });

        this.formAreaElement.addEventListener('dragleave',() => {
            this.formAreaElement.classList.remove('dragover');
            if (this.placeholder.parentNode === this.formAreaElement) {
                this.formAreaElement.removeChild(this.placeholder);
            }
        });

        this.formAreaElement.addEventListener('drop', event => {
            event.preventDefault();

            this.formAreaElement.classList.remove('dragover');
            // Remove the placeholder from the DOM
            if (this.placeholder !==null && this.placeholder.parentNode) {
                this.placeholder.parentNode.removeChild(this.placeholder);
            }

            const data = JSON.parse(this.app.dragData);

            if (data.source === 'select') {
                // Create a new component instance from the palette.
                const newComponent = this.createComponent(data.component);
                // Insert at the calculated position.
                const afterElement = this.getDragAfterElement(this.formAreaElement, event.clientY);
                if (afterElement == null) {
                    this.formAreaElement.appendChild(newComponent);
                } else {
                    this.formAreaElement.insertBefore(newComponent, afterElement);
                }
            }
            else if (data.source==='form') {
                const draggingElement = this.formAreaElement.querySelector('.dragging');
                const afterElement = this.getDragAfterElement(this.formAreaElement, event.clientY);
                if (afterElement == null) {
                    this.formAreaElement.appendChild(draggingElement);
                } else {
                    this.formAreaElement.insertBefore(draggingElement, afterElement);
                }
            }
        });
    }

    getDragAfterElement(container, y) {
        const draggableElements = [...container.querySelectorAll('.builder-form-component-wrapper:not(.dragging)')];
        return draggableElements.reduce((closest, child) => {
            const box = child.getBoundingClientRect();
            const offset = y - (box.top + box.height / 2);
            if (offset < 0 && offset > closest.offset) {
                return { offset: offset, element: child };
            }
            return closest;
        }, { offset: Number.NEGATIVE_INFINITY }).element;
    }

    setNamespace(namespace) {
        this.formAreaElement.setAttribute('data-namespace', namespace);
    }

    createComponent(componentType) {
        const element = document.createElement('div');
        element.classList.add('builder-form-component-wrapper');
        element.draggable = true;
        element.style.position = 'relative';
        element.style.minHeight = "40px";

        let ignoreMainClick = false;
        const closeElem = document.createElement('div')
        closeElem.style.position = 'absolute';
        closeElem.style.right = '6px';
        closeElem.style.top = '6px';
        closeElem.textContent = 'X';
        closeElem.style.padding="0 4px 0";
        closeElem.style.fontFamily = "arial";
        closeElem.style.borderRadius = "100%";
        closeElem.style.border = "1px solid black"
        closeElem.style.cursor = "pointer";
        closeElem.addEventListener('click', (evt)=> {
            ignoreMainClick=true;
            this.app.formInfoPanel.clearDynamicInfo();
            element.remove();
        })
        element.appendChild(closeElem);

        const label = document.createElement("label");
        label.textContent = componentType;
        label.style.pointerEvents = 'none';
        element.appendChild(label);

        let definition = Deform.getComponent('DeformComponent'+componentType);
        const component = document.createElement(definition.name);
        component.setAttribute('name', definition.name);
        const attributes = definition.metadata;
        Object.keys(attributes).forEach((key) => {
            if (key!=='slot' && key!=='error') {
                let attribute = attributes[key];
                if (attribute.name==='name') {
                    component.setAttribute('name', definition.name.substring(7));
                }
                else if (attribute.name==='value') {
                    let setValue = attribute.default ?? "";
                    component.setAttribute(key, setValue);
                }
                else if (attribute.type==='string' || attribute.type==='int' || attribute.type==='float') {
                    component.setAttribute(key, attribute['default'] ?? key);
                }
                else if (attribute.type==='array') {
                    component.setAttribute(key, attribute['default'] ?? '["'+key+'"]');
                }
                else if (attribute.type==='keyvalue-array') {
                    component.setAttribute(key, attribute['default'] ?? JSON.stringify([[key+"1", key+"1"],[key+"2",key+"2"]]));
                }
            }
            else if (key==='error') {
                component.setAttribute('error','');
            }
        });
        if ('slot' in attributes) {
            component.innerText = attributes.slot.type;
        }
        element.appendChild(component);

        element.addEventListener('dragstart', event => {
            event.dataTransfer.effectAllowed = 'move';
            this.app.dragData = JSON.stringify({
                component: componentType,
                source: 'form'
            });
            element.classList.add('dragging');
            if (this.app.selectedComponent!==null) {
                // this.formAreaElement.querySelectorAll('.selected').forEach(element=> {
                //     element.classList.remove('selected');
                // })
                this.app.clearSelectedComponent();
            }
        });

        element.addEventListener('dragend', (event) => {
            element.classList.remove('dragging');
        });

        element.addEventListener('click', event => {
            if (ignoreMainClick) {
                ignoreMainClick = false;
                return;
            }
            if (!event.target.classList.contains('dragging')) {
                // if (selectedComponent && selectedComponent !== event.target) {
                //     selectedComponent.parentElement.classList.remove('selected');
                // }
                const selectedComponent= event.target.classList.contains('builder-form-component-wrapper')
                    ? event.target.lastChild
                    : event.target
                if (this.app.selectedComponent === selectedComponent) {
                    return;
                }
                this.app.setSelectedComponent(selectedComponent, definition);
            }
        });
        return element;
    }
}

DeformSandbox.FormInfoPanel = class {
    constructor(app, formInfoElement) {
        this.app = app;
        this.formInfoElement = formInfoElement;
        this.dynamicComponentInfo = this.formInfoElement.querySelector('#dynamic-component-info');
        const namespaceInput = this.formInfoElement.querySelector("#form-namespace-input")
        if (namespaceInput) {
            namespaceInput.addEventListener('change', (evt) => {
                this.app.formAreaPanel.setNamespace( evt.target.value);
            });
        }
    }

    updateSelectedComponent()
    {
        const selectedComponent = this.app.selectedComponent;
        const metadataAttributes = this.app.selectedComponentObject.metadata;

        // full name
        const nameContainerElement = document.createElement('div');
        const label = document.createElement("label");
        label.textContent = "name";
        nameContainerElement.appendChild(label);
        this.fullNameInput = document.createElement('input');
        this.fullNameInput.type = 'text';
        this.fullNameInput.readOnly = true;
        this.fullNameInput.disabled = true;
        this.fullNameInput.value = selectedComponent.getComponentFullName();
        nameContainerElement.appendChild(this.fullNameInput);
        this.dynamicComponentInfo.append(nameContainerElement)

        const sortedMetadataAttributes = Object.values(metadataAttributes).sort((a, b) => {
            if (a.name === 'name') return -1;
            if (b.name === 'name') return 1;
            return a.name.localeCompare(b.name);
        });

        const observerAttributeElementsByName = {};
        Object.values(sortedMetadataAttributes).forEach(attribute => {
            if (attribute.type==='file') {
                console.warn("attribute of type 'file' not yet supported!");
                return;
            }
            const attributeElement = this.addAttributeElement(attribute);
            if (attribute.name === 'value') {
                // event listener for 'value' or 'slot'
                const attributeEventListener = ((evt) => {
                    if (attribute.type === 'boolean') {
                        attributeElement.checked = evt.detail.value!=='';
                    }
                    else {
                        attributeElement.value = evt.detail.value;
                    }
                });
                selectedComponent.addEventListener("deform:change", attributeEventListener);
                this.app.selectedComponentListeners.push({
                    element: selectedComponent,
                    listener:attributeEventListener
                });
            }
            else if (attribute.name === 'slot') {
                if (selectedComponent.constructor.name==='deform-text-area') {
                    const attributeEventListener = ((evt) => {
                        attributeElement.value = evt.detail.value;
                    });
                    selectedComponent.addEventListener("deform:change", attributeEventListener);
                    this.app.selectedComponentListeners.push({
                        element:selectedComponent,
                        listener:attributeEventListener
                    });
                }
                else {
                    // slot is likely only used for presentation
                    // console.warn("'not observing or watching 'deform:change' for " + selectedComponent.constructor.name);
                }
            }
            else {
                observerAttributeElementsByName[attribute.name] = attributeElement;
            }
        });

        const observerAttributeKeys = Object.keys(observerAttributeElementsByName);
        if (observerAttributeKeys.length > 0) {
            // add mutation observer for attributes other than 'value' and 'name'
            const componentObserver = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.type === 'attributes' && observerAttributeKeys.includes(mutation.attributeName)) {
                        if (selectedComponent && mutation.attributeName === 'name') {
                            this.fullNameInput.value = selectedComponent.getAttribute(mutation.attributeName);
                        }
                        else if (selectedComponent && !selectedComponent.isGuarded(mutation.attributeName)) {
                            const attribute = metadataAttributes[mutation.attributeName];
                            const attributeElement = metadataAttributes[mutation.attributeName];
                            selectedComponent.guard(attribute.name);
                            if (attribute.name === 'name') {
                                this.fullNameInput.value = selectedComponent.getAttribute("name");
                                attributeElement.value = selectedComponent.getComponentBaseName();
                            } else
                            if (attribute.type === 'boolean') {
                                attributeElement.checked = selectedComponent.getAttribute(mutation.attributeName) !== '';
                            } else {
                                const value = (attribute.name === 'name')
                                    ? selectedComponent.getComponentBaseName()
                                    : selectedComponent.getAttribute(attribute.name);
                                if (value !== attributeElement.value) {
                                    attributeElement.value = value;
                                }
                            }
                            requestAnimationFrame(() => {
                                selectedComponent.unguard(mutation.attributeName);
                            })
                        }
                    }
                });
            });
            this.app.selectedComponentObservers.push(componentObserver);
            componentObserver.observe(selectedComponent,{
                attributes: true,
                attributeFilter: observerAttributeKeys
            });
        }

        const eventLogElement = document.createElement("textarea");
        eventLogElement.style.marginTop = "8px";
        eventLogElement.style.width="100%";
        eventLogElement.style.minHeight = "100px";
        const deformChangeListener = ((evt) => {
            eventLogElement.value += "deform:change "+evt.target.getAttribute('name') + " value=" + evt.detail.value + "\r\n";
            eventLogElement.scrollTop = eventLogElement.scrollHeight;
        });
        this.app.selectedComponentListeners.push({
            element: selectedComponent,
            listener: deformChangeListener
        });
        selectedComponent.addEventListener("deform:change", deformChangeListener);
        this.dynamicComponentInfo.appendChild(eventLogElement)
    }

    clearDynamicInfo()
    {
        this.dynamicComponentInfo.innerHTML = '';
    }

    addAttributeElement(attribute)
    {
        const selectedComponent = this.app.selectedComponent;

        const attrDiv = document.createElement("div");
        const label = document.createElement("label");
        label.innerText = attribute.name === 'name' ? 'basename' : attribute.name;
        attrDiv.appendChild(label);
        this.dynamicComponentInfo.appendChild(attrDiv);
        let attributeElement;
        if (attribute.options !== null ) {
            attributeElement = document.createElement("select");
            attribute.options.forEach((option) => {
                const optionElement = document.createElement('option');
                optionElement.value = option;
                optionElement.textContent = option;
                attributeElement.appendChild(optionElement);
            });
            attributeElement.value = attribute.default;
        }
        else if (attribute.type==='boolean') {
            attributeElement = document.createElement("input");
            attributeElement.type = "checkbox";
            attributeElement.value = "true";
            attributeElement.name = attribute.name;
        }
        else {
            attributeElement = attribute.type === 'textarea'
                ? document.createElement('textarea')
                : document.createElement("input")
            attributeElement.setAttribute("name", attribute.name);
        }
        attrDiv.appendChild(attributeElement);

        if (attribute.name==='slot') {
            if (selectedComponent.shadowRoot) {
                const slot = selectedComponent.shadowRoot.querySelector("slot");
                const assignedNodes = slot.assignedNodes({flatten: true});
                attributeElement.value = assignedNodes.map(node => {
                    if (node.nodeType === Node.ELEMENT_NODE) {
                        return node.outerHTML;
                    } else if (node.nodeType === Node.TEXT_NODE) {
                        return node.textContent;
                    } else {
                        return '';
                    }
                }).join('');}
        }
        else if (attribute.name==='name') {
            attributeElement.value = selectedComponent.getComponentBaseName();
        }
        else if (selectedComponent.hasAttribute(attribute.name)) {
            if (attribute.type==='boolean') {
                attributeElement.checked = selectedComponent.getAttribute(attribute.name)!=='';
            }
            else {
                attributeElement.value = selectedComponent.getAttribute(attribute.name);
            }
        }

        attributeElement.addEventListener('change',evt => {
            try {
                if (!selectedComponent.isGuarded(attribute.name)) {
                    selectedComponent.guard(attribute.name);
                    if (attribute.name === 'slot') {
                        selectedComponent.textContent = evt.target.value;
                    } else if (attribute.name === 'name') {
                        selectedComponent.setComponentBaseName(evt.target.value);
                        selectedComponent.triggerNameUpdated();
                    } else if (attribute.type === 'string') {
                        selectedComponent.setAttribute(attribute.name, evt.target.value);
                    } else if (attribute.type === 'keyvalue-array') {
                        selectedComponent.setAttribute(attribute.name, evt.target.value);
                    } else if (attribute.type === 'array') {
                        selectedComponent.setAttribute(attribute.name, evt.target.value);
                    } else if (attribute.type === 'integer') {
                        selectedComponent.setAttribute(attribute.name, evt.target.value);
                    } else if (attribute.type === 'float') {
                        selectedComponent.setAttribute(attribute.name, evt.target.value);
                    } else if (attribute.type === 'boolean') {
                        selectedComponent.setAttribute(attribute.name, evt.target.checked ? evt.target.value : '');
                    } else if (attribute.type === 'textarea') {
                        selectedComponent.setAttribute(attribute.name, evt.target.value);
                    } else {
                        console.error("Unrecognised attribute type '" + attribute.type + "'");
                    }
                    requestAnimationFrame(() => {
                        selectedComponent.unguard(attribute.name);
                    });
                }
            }
            catch(err) {
                console.error(err);
            }
        });
        return attributeElement;
    }
}



