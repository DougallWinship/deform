<?php

declare(strict_types=1);

namespace Deform\Component\Shadow;

trait Currency
{
    public function getShadowTemplate(): string
    {
        return parent::getShadowTemplate() . "<style>.control-container {display:flex;flex-direction: row}</style>";
    }

    public function mergeShadowAttributes(&$attributes): void
    {
        $attributes["value"]->default = "0";
        $attributes['currency'] = new Attribute(
            'currency',
            '.currency-symbol',
            Attribute::TYPE_STRING,
            "element.innerHTML=this.getAttribute('currency');",
            "element.innerHTML=newValue;",
            default: "&pound;"
        );
        $attributes["min"] = new Attribute(
            "min",
            ".control-container input",
            Attribute::TYPE_INTEGER,
            "element.dataset.min = this.getAttribute('min');",
            "element.dataset.min = newValue;",
            Attribute::BEHAVIOUR_VISIBLE_IF_EMPTY,
            "0"
        );
        $attributes["max"] = new Attribute(
            "max",
            ".control-container input",
            Attribute::TYPE_INTEGER,
            "element.dataset.max = this.getAttribute('max');",
            "element.dataset.max = newValue;",
            Attribute::BEHAVIOUR_VISIBLE_IF_EMPTY,
            ""
        );
        $attributes["dp"] = new Attribute(
            "dp",
            ".control-container input",
            Attribute::TYPE_INTEGER,
            "element.dataset.dp = this.getAttribute('dp');",
            "element.dataset.dp = newValue;",
            Attribute::BEHAVIOUR_VISIBLE_IF_EMPTY,
            "2"
        );
        $attributes["round"] = new Attribute(
            "round",
            ".control-container input",
            Attribute::TYPE_STRING,
            "element.dataset.round = this.getAttribute('round');",
            "element.dataset.round = newValue;",
            Attribute::BEHAVIOUR_VISIBLE_IF_EMPTY,
            "standard",
            options: ["standard","ceil","floor","bankers"]
        );
    }
}
