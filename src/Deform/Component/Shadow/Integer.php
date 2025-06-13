<?php

declare(strict_types=1);

namespace Deform\Component\Shadow;

trait Integer
{
    public function mergeShadowAttributes(&$attributes): void
    {
        $attributes["value"]->default = "0";
        $attributes["min"] = new Attribute(
            "min",
            ".control-container input",
            Attribute::TYPE_INTEGER,
            "element.min = parseInt(this.getAttribute('min'));",
            "element.min = newValue;",
            Attribute::BEHAVIOUR_VISIBLE_IF_EMPTY,
            ""
        );
        $attributes["max"] = new Attribute(
            "max",
            ".control-container input",
            Attribute::TYPE_INTEGER,
            "element.setAttribute('max',this.getAttribute('max'));",
            "element.setAttribute('max',newValue);",
            Attribute::BEHAVIOUR_VISIBLE_IF_EMPTY,
            ""
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
    }
}
