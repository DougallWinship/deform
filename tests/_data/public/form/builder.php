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
        <?php break; } ?>
    </div>

    <form id="form-area">
        <h3>Form Area</h3>
        <p>Drag components here</p>
    </form>

    <div id="form-info" style="">
        <h3>Form Info</h3>
        <div id="form-definition">
            <p>Details about the form & any selected component will appear here.</p>
        </div>
        <div id="dynamic-component-info">
        </div>
    </div>
</div>

<script>
    // Create a placeholder element to show drop target indicator
    const placeholder = document.createElement('div');
    placeholder.className = 'drop-placeholder';

    let selectedComponent = null;
    let selectedComponentObject = null;

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

        let definition = DeformComponentRegistry['DeformComponent'+componentType];
        //console.log(definition.name);
        const component = document.createElement(definition.name)

        const attributes = definition.metadata.attributes;
        Object.keys(attributes).forEach((key) => {
            if (key!=='slot' && key!=='error') {
                let value = attributes[key];
                if (value==='string') {
                    component.setAttribute(key, key);
                }
                else if (value==='array') {
                    component.setAttribute(key, "['"+key+"']");
                }
                else if (value==='json') {
                    component.setAttribute(key, JSON.stringify({key: key}));
                }
            }
        });
        if ('slot' in attributes) {
            component.innerText = attributes.slot;
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
                updateFormInfo();

            }
        });
        elem.addEventListener('dragend', event => {
            elem.classList.remove('dragging');
        });
        elem.addEventListener('click', event => {
            if (!event.target.classList.contains('dragging')) {
                if (selectedComponent && selectedComponent !== event.target) {
                    selectedComponent.parentElement.classList.remove('selected');
                }
                selectedComponent = event.target;
                selectedComponent.parentElement.classList.add('selected');
                selectedComponentObject = definition;
                updateFormInfo()
            }
        });
        return elem;
    }

    function updateFormInfo()
    {
        const componentInfo = document.getElementById("dynamic-component-info");
        componentInfo.innerHTML = '';
        if (selectedComponent===null){
            return;
        }
        const elem = document.createElement("h3");
        elem.innerText = "Component : " + selectedComponentObject.name;
        componentInfo.appendChild(elem);

        const attributes = selectedComponentObject.metadata.attributes;
        Object.keys(attributes).forEach((key) => {
            const attrDiv = document.createElement("div");
            const label = document.createElement("label");
            label.innerText = key;
            attrDiv.appendChild(label);
            componentInfo.appendChild(attrDiv);
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
            else if (selectedComponent.hasAttribute(key)) {
                input.setAttribute('value', selectedComponent.getAttribute(key));
            }
            input.addEventListener('change',evt => {
                try {
                    if (key === 'slot') {
                        selectedComponent.innerHTML = evt.target.value;
                    } else if (attributes[key] === 'string') {
                        selectedComponent.setAttribute(key, evt.target.value);
                    } else if (attributes[key] === 'json') {
                        JSON.parse(evt.target.value);
                        selectedComponent.setAttribute(key, evt.target.value);
                    } else if (attributes[key] === 'array') {
                        JSON.parse(evt.target.value);
                        selectedComponent.setAttribute(key, evt.target.value);
                    } else if (attributes[key] === 'number') {
                        selectedComponent.setAttribute(key, evt.target.value);
                    }
                }
                catch(err) {
                    console.log(err);
                    alert(JSON.stringify(err));
                }
            })
        })
    }
</script>
