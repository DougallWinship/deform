<?php

declare(strict_types=1);

namespace Deform\Component\Shadow;

trait Currency
{
    public function shadowJavascript(): array
    {
        return [
                '.currency-symbol' => <<<JS
if (this.hasAttribute('currency')) {
    element.innerHTML = this.getAttribute('currency')
}
else {
    element.style.display = 'none';
}
JS
            ] + parent::shadowJavascript();
    }

    public function mergeAttributeMetadata(): array
    {
        return [
            'currency' => 'string'
        ];
    }
}
