<?php

declare(strict_types=1);

namespace Deform\Component\Shadow;

trait ColourSelector
{
    public function mergeShadowAttributes(&$attributes): void
    {
        $attributes['value']->default = "#000000";
    }
}