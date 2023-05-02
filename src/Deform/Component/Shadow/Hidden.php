<?php

declare(strict_types=1);

namespace Deform\Component\Shadow;

trait Hidden
{
    public function shadowJavascript(): array
    {
        return [
            'input' => <<<JS
if (this.hasAttribute('value')) {
    this.internals_.setFormValue(this.getAttribute('value'));
}
JS
        ];
    }
}