<script>
<?= \Deform\Component\ComponentFactory::getCustomElementDefinitionsJavascript(false); ?>
</script>
<div id="form-builder">
    <div id="available-components">
        <h3>Available Components</h3>
        <?php foreach(\Deform\Component\ComponentFactory::getRegisteredComponents() as $component){ ?>
            <div
                    class="component component-<?= $component ?>"
                    draggable="true"
                    data-component="<?= $component ?>">
                <?= $component ?>
            </div>
        <?php } ?>
    </div>

    <form id="form-area" data-namespace="builder" method="post">
        <h3>Form Area</h3>
        <p>Drag components here</p>
        <input type="submit" value="Submit"/>
    </form>

    <div id="form-info" style="">
        <h3>Form Info</h3>
        <div id="form-definition">
            <label>Form namespace<input id="form-namespace-input" type="text" value="builder" autocomplete="off" /></label>
            <p>Details about the form & any selected component will appear here.</p>
        </div>
        <div id="dynamic-component-info">
        </div>
    </div>
</div>

<script>
    document.addEventListener("formdata", (event) => {
        const entries = event.formData.entries();
        console.log("FormData:");
        for (var pair of entries) {
            console.log("  "+pair[0]+"="+pair[1]);
        }
    });

    // Create a placeholder element to show drop target indicator
    const placeholder = document.createElement('div');
    placeholder.className = 'drop-placeholder';

    let selectedComponent = null;
    let selectedComponentObject = null;
    let selectedComponentNameObservers = null;

    // Helper: Get the element after which the dragged element should be inserted
    function getDragAfterElement(container, y) {
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

    // Set up drag events for palette items (available components)
    document.querySelectorAll('#available-components .component').forEach(elem => {
        elem.addEventListener('dragstart', event => {
            event.dataTransfer.effectAllowed = 'copy';
            event.dataTransfer.setData('text/plain', event.target.getAttribute('data-component'));
            event.dataTransfer.setData('source', 'select');
            event.target.classList.add('dragging');
        });
        elem.addEventListener('dragend', event => {
            event.target.classList.remove('dragging');
        });
    });

    // Reference to the form area
    const formArea = document.getElementById('form-area');

    // Dragover: update the drop target indicator position
    formArea.addEventListener('dragover', event => {
        event.preventDefault();
        const source = event.dataTransfer.getData('source');
        event.dataTransfer.dropEffect = (source === 'select') ? 'copy' : 'move';
        formArea.classList.add('dragover');

        // Calculate insertion point and position the placeholder
        const afterElement = getDragAfterElement(formArea, event.clientY);
        if (afterElement == null) {
            formArea.appendChild(placeholder);
        } else {
            formArea.insertBefore(placeholder, afterElement);
        }
    });

    formArea.addEventListener('dragleave', event => {
        formArea.classList.remove('dragover');
        // Remove the placeholder when leaving the drop zone
        if (placeholder.parentNode === formArea) {
            placeholder.parentNode.removeChild(placeholder);
        }
    });

    // Drop: remove indicator and move or create the element
    formArea.addEventListener('drop', event => {
        event.preventDefault();
        formArea.classList.remove('dragover');
        // Remove the placeholder from the DOM
        if (placeholder.parentNode) {
            placeholder.parentNode.removeChild(placeholder);
        }

        const source = event.dataTransfer.getData('source');
        if (source === 'select') {
            // Create a new component instance from the palette.
            const componentType = event.dataTransfer.getData('text/plain');
            const newComponent = createComponent(componentType);
            // Insert at the calculated position.
            const afterElement = getDragAfterElement(formArea, event.clientY);
            if (afterElement == null) {
                formArea.appendChild(newComponent);
            } else {
                formArea.insertBefore(newComponent, afterElement);
            }
        } else if (source === 'form') {
            // For reordering: move the existing dragged element.
            const draggingElement = document.querySelector('.dragging');
            const afterElement = getDragAfterElement(formArea, event.clientY);
            if (afterElement == null) {
                formArea.appendChild(draggingElement);
            } else {
                formArea.insertBefore(draggingElement, afterElement);
            }
        }
    });

    // Function to create a new component instance for the form area.
    function createComponent(componentType) {
        const elem = document.createElement('div');
        elem.className = 'builder-form-component-wrapper';
        elem.setAttribute('draggable', 'true');
        elem.style.position = 'relative';
        elem.style.minHeight = "40px";

        let ignoreMainClick = false;
        const closeElem = document.createElement('div')
        closeElem.style.position = 'absolute';
        closeElem.style.right = '6px';
        closeElem.style.top = '6px';
        closeElem.textContent = 'X';
        closeElem.style.padding="1px 4px";
        closeElem.style.fontFamily = "arial";
        closeElem.style.borderRadius = "100%";
        closeElem.style.border = "1px solid black"
        closeElem.style.cursor = "pointer";
        closeElem.addEventListener('click', (evt)=> {
            ignoreMainClick=true;
            removeFormInfo();
            elem.remove();
        })
        elem.appendChild(closeElem);

        const label = document.createElement("label");
        label.textContent = componentType;
        elem.appendChild(label);

        let definition = DeformComponentRegistry['DeformComponent'+componentType];
        const component = document.createElement(definition.name);
        component.setAttribute('name', definition.name);
        const attributes = definition.metadata;
        Object.keys(attributes).forEach((key) => {
            if (key!=='slot' && key!=='error') {
                let attribute = attributes[key];
                if (attribute.type==='string' || attribute.type==='int' || attribute.type==='float') {
                    component.setAttribute(key, key);
                }
                else if (attribute.type==='array') {
                    component.setAttribute(key, '["'+key+'"]');
                }
                else if (attribute.type==='keyvalue-array') {
                    component.setAttribute(key, JSON.stringify([[key+"1", key+"1"],[key+"2",key+"2"]]));
                }
            }
            else if (key==='error') {
                component.setAttribute('error','');
            }
        });
        if ('slot' in attributes) {
            component.innerText = attributes.slot.type;
        }
        elem.appendChild(component);

        // Add drag events for reordering.
        elem.addEventListener('dragstart', event => {
            event.dataTransfer.effectAllowed = 'move';
            event.dataTransfer.setData('source', 'form');
            elem.classList.add('dragging');
            if (selectedComponent!==null) {
                selectedComponent.parentElement.classList.remove('selected');
                selectedComponent = null;
                selectedComponentObject = null;
                updateFormComponentInfo();
            }
        });
        elem.addEventListener('dragend', event => {
            elem.classList.remove('dragging');
        });
        elem.addEventListener('click', event => {
            if (ignoreMainClick) {
                ignoreMainClick=false;
                return;
            }
            if (!event.target.classList.contains('dragging')) {
                if (selectedComponent && selectedComponent !== event.target) {
                    selectedComponent.parentElement.classList.remove('selected');
                }
                if (!event.target.classList.contains('removed')) {
                    selectedComponent = event.target.classList.contains('builder-form-component-wrapper')
                        ? event.target.lastChild
                        : event.target
                    selectedComponent.parentElement.classList.add('selected');
                    selectedComponentObject = definition;
                    updateFormComponentInfo()
                }
            }
        });
        return elem;
    }

    function removeFormInfo() {
        const componentInfo = document.getElementById("dynamic-component-info");
        componentInfo.innerHTML = '';

        document.querySelectorAll(".builder-form-component-wrapper.selected").forEach((node) => {
            node.classList.remove('selected');
        })
        if (selectedComponent) {
            selectedComponent.classList.remove('selected');
            selectedComponent = null;
            selectedComponentObject = null;
            selectedComponentNameObservers.forEach((observer) => {
                observer.disconnect();
            });
        }
    }

    function updateFormComponentInfo()
    {
        const componentInfo = document.getElementById("dynamic-component-info");
        componentInfo.innerHTML = '';
        if (selectedComponent===null){
            return;
        }
        if (selectedComponentNameObservers!==null) {
            selectedComponentNameObservers.forEach((observer)=>{
                observer.disconnect();
            });
        }
        const componentInfoForm = document.createElement('form');


        const elem = document.createElement("h3");
        elem.style.marginBottom = "2px";
        elem.innerText = "Component : " + selectedComponentObject.name;
        componentInfo.appendChild(elem);

        const nameDiv = document.createElement("div");
        nameDiv.style.marginBottom = "2px";
        const nameLabel = document.createElement("label");
        nameLabel.innerHTML = "name";
        const nameInput = document.createElement("input");
        nameInput.disabled = true;
        nameInput.value = selectedComponent.getAttribute('name');
        nameDiv.appendChild(nameLabel);
        nameDiv.appendChild(nameInput);
        componentInfoForm.appendChild(nameDiv);
        const nameObserver = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.type==='attributes' && mutation.attributeName==='name') {
                    nameInput.value = mutation.target.getAttribute('name');
                }
            })
        });
        nameObserver.observe(selectedComponent, {
            attributes: true,
            attributeFilter: ['name']
        });
        if (selectedComponentNameObservers===null) {
            selectedComponentNameObservers = [];
        }
        selectedComponentNameObservers.push(nameObserver);

        const attributes = selectedComponentObject.metadata;
        // console.log(selectedComponent, selectedComponentObject);
        Object.keys(attributes).forEach((key) => {
            const attrDiv = document.createElement("div");
            const label = document.createElement("label");
            label.innerText = key === 'name' ? 'basename' : key;
            attrDiv.appendChild(label);
            componentInfoForm.appendChild(attrDiv);
            const input = document.createElement("input")
            input.setAttribute("name", key);
            attrDiv.appendChild(input);

            if (key==='slot') {
                if (selectedComponent.shadowRoot) {
                    const slot = selectedComponent.shadowRoot.querySelector("slot");
                    const assignedNodes = slot.assignedNodes({flatten: true});
                    const slotHtml = assignedNodes.map(node => {
                        if (node.nodeType === Node.ELEMENT_NODE) {
                            return node.outerHTML;
                        }
                        else if (node.nodeType === Node.TEXT_NODE) {
                            return node.textContent;
                        }
                        else {
                            return '';
                        }
                    }).join('');
                    input.setAttribute('value', slotHtml);
                }
            }
            else if (key==='name') {
                input.setAttribute('value',selectedComponent.getComponentBaseName());
            }
            else if (selectedComponent.hasAttribute(key)) {
                input.setAttribute('value', selectedComponent.getAttribute(key));
            }
            // else {
            //     console.warn("can't find attribute '"+key+"'", selectedComponent);
            // }
            input.addEventListener('change',evt => {
                try {
                    if (key === 'slot') {
                        selectedComponent.innerHTML = evt.target.value;
                    }
                    else if (key === 'name') {
                        selectedComponent.setComponentBaseName(evt.target.value);
                        selectedComponent.triggerNameUpdated();
                    }
                    else if (attributes[key].type === 'string') {
                        selectedComponent.setAttribute(key, evt.target.value);
                    } else if (attributes[key].type === 'keyvalue-array') {
                        selectedComponent.setAttribute(key, evt.target.value);
                    } else if (attributes[key].type === 'array') {
                        selectedComponent.setAttribute(key, evt.target.value);
                    } else if (attributes[key].type === 'int') {
                        selectedComponent.setAttribute(key, evt.target.value);
                    } else if (attributes[key].type === 'float') {
                        selectedComponent.setAttribute(key, evt.target.value);
                    } else if (attributes[key].type === 'boolean') {
                        selectedComponent.setAttribute(key, evt.target.value);
                    } else {
                        console.error("Unrecognised attribute type '"+attributes[key].type+"'");
                    }
                }
                catch(err) {
                    console.error(err);
                }
            });
            const inputObserver = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.type==='attributes' && mutation.attributeName===key) {
                        const value = key==='name'
                            ? selectedComponent.getComponentBaseName()
                            : selectedComponent.getAttribute(key);
                        if (value !== input.value) {
                            input.value = value;
                        }
                    }
                })
            });
            inputObserver.observe(selectedComponent,{
                attributes: true,
                attributeFilter: [key]
            });
        });
        componentInfo.appendChild(componentInfoForm);
    }

    document.getElementById('form-namespace-input').addEventListener('change', (evt) => {
        formArea.setAttribute('data-namespace', evt.target.value)
    })
</script>
<style>
    #dynamic-component-info form {
        display:table
    }
    #dynamic-component-info form>div {
        display:table-row;
    }
    #dynamic-component-info form>div>* {
        display:table-cell;
    }
    #dynamic-component-info form>div>*:first-child {
        text-align:right;
        padding-right:4px;
    }
</style>
