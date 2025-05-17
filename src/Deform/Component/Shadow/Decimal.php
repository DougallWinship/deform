<?php

declare(strict_types=1);

namespace Deform\Component\Shadow;

trait Decimal
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
        $attributes["dp"] = new Attribute(
            "dp",
            ".control-container input",
            Attribute::TYPE_INTEGER,
            "element.dataset.dp = this.getAttribute('dp');",
            "element.dataset.dp = newValue;",
            Attribute::BEHAVIOUR_VISIBLE_IF_EMPTY,
            "0"
        );
        $attributes["round"] = new Attribute(
            "round",
            ".control-container input",
            Attribute::TYPE_STRING,
            "element.dataset.round = this.getAttribute('round');",
            "element.dataset.round = newValue;",
            Attribute::BEHAVIOUR_VISIBLE_IF_EMPTY,
            "standard"
        );
    }
}
