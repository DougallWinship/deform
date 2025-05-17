<?php

declare(strict_types=1);

namespace Deform\Component;

use Deform\Component\Shadow\ColourSelector;

/**
 *
 */
class ColorSelector extends Input
{
    use ColourSelector;

    /**
     * @inheritDoc
     */
    public function setup(): void
    {
        parent::setup();
        $this->type('color');
    }
}
