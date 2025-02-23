<?php

declare(strict_types=1);

namespace Deform\Component\Shadow;

trait Submit
{
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
