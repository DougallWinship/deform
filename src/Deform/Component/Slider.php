<?php

declare(strict_types=1);

namespace Deform\Component;

use Deform\Html\Html;

/**
 * @method $this min(int $min)
 * @method $this max(int $max)
 * @method $this step(mixed $step) usually an int a float or 'any'
 */
class Slider extends Input
{
    /**
     * @inheritDoc
     */
    public function setup()
    {
        parent::setup();
        $this->input->type('range');
    }

    public function hydrate()
    {
    }

    /**
     * whether to add an output tag showing the current value
     * @param bool $showOutput
     * @return $this|void
     */
    public function showOutput(bool $showOutput = true) : self
    {
        if ($showOutput) {
            $this->componentContainer->control->addHtmlTag(Html::output(['class' => 'slider-output']));
            $input = $this->componentContainer->control->getControls()[0];
            $input->oninput("this.nextElementSibling.value=this.value");
            return $this;
        }
    }

    public function shadowJavascript(): array
    {
        return [
                '.control-container input' => <<<JS
element.id = id;
element.name = name;
if (this.hasAttribute('value')) {
    element.value = this.getAttribute('value');
}
if (this.hasAttribute('min')) {
    element.setAttribute('min', this.getAttribute('min'))
}
if (this.hasAttribute('max')) {
    element.setAttribute('max', this.getAttribute('max'))
}
if (this.hasAttribute('showOutput')) {
    let showOutput = this.getAttribute('showOutput');
    let showOutputAsInt = parseInt(showOutput);
    if (showOutput.toLowerCase()!=='false' && (isNaN(showOutputAsInt) || showOutputAsInt!==0)) {
        let output = document.createElement('output');
        output.classList.add('slider-output');
        element.addEventListener('input', (evt)=> {
            output.value = evt.target.value;
        });
        element.parentNode.appendChild(output);
    }
}
JS
            ] + parent::shadowJavascript();
    }
}
