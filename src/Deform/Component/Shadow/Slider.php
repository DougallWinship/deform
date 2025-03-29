<?php

declare(strict_types=1);

namespace Deform\Component\Shadow;

trait Slider
{
    public function mergeShadowAttributes(): array
    {
        $attributes = [];
        $attributes["min"] = new Attribute(
            "min",
            ".control-container input",
            Attribute::TYPE_INTEGER,
            "element.setAttribute('min',this.getAttribute('min'));",
            "element.setAttribute('min',newValue);",
            Attribute::BEHAVIOUR_VISIBLE_IF_EMPTY,
            "0"
        );
        $attributes["max"] = new Attribute(
            "max",
            ".control-container input",
            Attribute::TYPE_INTEGER,
            "element.setAttribute('max',this.getAttribute('max'));",
            "element.setAttribute('max',newValue);",
            Attribute::BEHAVIOUR_VISIBLE_IF_EMPTY,
            "100"
        );
        $attributes["step"] = new Attribute(
            "step",
            ".control-container input",
            Attribute::TYPE_INTEGER,
            "element.setAttribute('step',this.getAttribute('step'));",
            "element.setAttribute('step',newValue);",
            Attribute::BEHAVIOUR_VISIBLE_IF_EMPTY,
            "1"
        );
        return $attributes;
    }
}
