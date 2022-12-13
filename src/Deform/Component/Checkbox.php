<?php

declare(strict_types=1);

namespace Deform\Component;

use Deform\Html\Html as Html;
use Deform\Html\HtmlTag;

/**
 * @method Checkbox checked(string $checked)
 * @persistAttribute inputLabelText
 */
class Checkbox extends Input
{
    public ?string $inputLabelText = null;

    /** @var HtmlTag $inputLabel */
    public $inputLabel;

    /**
     * @inheritDoc
     */
    public function setup()
    {
        $this->autoAddControl = false;
        parent::setup();
        $this->type('checkbox');
        $this->input->value('1'); // default ... it's easy to change but doesn't yet have hydration support
        $this->inputLabel = Html::label(['for' => $this->getId()])->add($this->fieldName);
        $this->addControl($this->input, [
            $this->input,
            ' ',
            $this->inputLabel,
        ]);
        $this->componentContainer->disableLabel = true;
        $this->addExpectedField($this->fieldName);
    }

    /**
     * @param string $text
     * @return $this
     * @throws \Exception
     */
    public function text(string $text): self
    {
        $this->inputLabelText = $text;
        $this->inputLabel->reset($text);
        return $this;
    }

    /**
     * for convenience since it's a special case
     * @param string $text
     * @return $this
     * @throws \Exception
     */
    public function label(string $text): self
    {
        return $this->text($text);
    }

    /**
     * @inheritDoc
     */
    public function hydrate()
    {
        if (is_string($this->inputLabelText)) {
            $this->text($this->inputLabelText);
        }
    }

    /**
     * @inheritDoc
     */
    public function setValue($value): self
    {
        if ($value) {
            $this->input->set('checked', 'checked');
        } else {
            $this->input->unset('checked');
        }
        return $this;
    }

    /**
     * @return string[]
     */
    public function shadowJavascript(): array
    {
        return
            [
            '.label-container label' => null,// explicitly remove this rule (not strictly necessary as it won't be found anyway!)
            '.control-container label' => <<<JS
if (this.hasAttribute('label')) {
    element.innerHTML = this.getAttribute('label')
}
else if (this.hasAttribute('value')) {
    element.innerHTML = this.getAttribute('value')
}
else if (this.hasAttribute('name')) {
    element.innerHTML = this.getAttribute('name')
}
element.setAttribute('for', id);
JS,
            '.control-container input' => <<<JS
element.id = id;
element.name = name;
if (this.hasAttribute('checked')) {
    let checked = this.getAttribute('checked');
    if (checked.toLowerCase()==='false' || parseInt(checked)===0) {
        element.checked = false;
    } 
    else {
        element.checked = true;
    }
}
else {
    element.checked = false
}
if (this.hasAttribute('value')) {
    element.value = this.getAttribute('value');
}
JS,
            '.component-container input[type=hidden]' => <<<JS
element.name= (namespaceAttr ? namespaceAttr+'[expected_data][]' : 'expected_data');
JS
        ] + parent::shadowJavascript();
    }
}
