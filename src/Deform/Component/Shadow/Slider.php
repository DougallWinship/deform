<?php

declare(strict_types=1);

namespace Deform\Component\Shadow;

trait Slider
{
    public function mergeShadowAttributes(): array
    {
        $attributes = [];
        $attributes["min"] =new Attribute(
            "min",
            ".control-container input",
            Attribute::TYPE_INTEGER,
            "element.setAttribute('min',this.getAttribute('min'));",
            ""
        );
        $attributes["max"] =new Attribute(
            "max",
            ".control-container input",
            Attribute::TYPE_INTEGER,
            "element.setAttribute('max',this.getAttribute('max'));",
            ""
        );
        $attributes["step"] =new Attribute(
            "max",
            ".control-container input",
            Attribute::TYPE_INTEGER,
            "element.setAttribute('step',this.getAttribute('step'));",
            ""
        );
        return $attributes;
    }
}
