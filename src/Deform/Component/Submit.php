<?php

declare(strict_types=1);

namespace Deform\Component;

class Submit extends Input
{
    /**
     * @inheritDoc
     */
    public function setup()
    {
        parent::setup();
        $this->autolabel(false);
        $this->type('submit');
        $this->value($this->fieldName);
        $this->componentContainer->controlOnly = true;
    }

    public function hydrate()
    {
    }

    public function shadowJavascript(): array
    {
        return [
            '.control-container input' => null,
            '#component-submit input' => <<<JS
element.id = id;
element.name = name;
if (this.hasAttribute('value')) {
    element.value = this.getAttribute('value');
}
JS
        ];
    }
}
