<?php

declare(strict_types=1);

namespace Deform\Component\Shadow;

trait Image
{
    public function shadowJavascript(): array
    {
        return ['.control-container input#hidden-image-namespace-name' => <<<JS
element.id = 'hidden-'+id;
element.name = name;
if (this.hasAttribute('value')) {
    element.value = this.getAttribute('value');
}
JS
            ] + parent::shadowJavascript();
    }
}
