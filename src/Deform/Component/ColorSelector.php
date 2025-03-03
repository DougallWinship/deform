<?php

declare(strict_types=1);

namespace Deform\Component;

/**
 *
 */
class ColorSelector extends Input
{
    use Shadow\ColorSelector;

    /**
     * @inheritDoc
     */
    public function setup(): void
    {
        parent::setup();
        $this->type('color');
    }
}
