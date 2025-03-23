<?php

declare(strict_types=1);

namespace Deform\Component\Shadow;

trait Currency
{
    public function getShadowTemplate(): string
    {
        return parent::getShadowTemplate() . "<style>.control-container {display:flex;flex-direction: row}</style>";
    }

    public function mergeShadowAttributes(): array
    {
        $attributes = [];
        $attributes['currency'] = new Attribute(
            'currency',
            '.currency-symbol',
            Attribute::TYPE_STRING,
            "element.innerHTML=this.getAttribute('currency');",
            "element.innerHTML=newValue;"
        );
        return $attributes;
    }
}
