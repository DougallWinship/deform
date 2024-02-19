<?php

declare(strict_types=1);

namespace Deform\Component;

/**
 *
 */
class ColorSelector extends Input
{
    /**
     * @inheritDoc
     */
    public function setup()
    {
        parent::setup();
        $this->type('color');
    }

    public function hydrate()
    {
    }
}
