<?php

declare(strict_types=1);

namespace Deform\Component;

/**
 *
 */
class Password extends Input
{
    /**
     * @inheritDoc
     */
    public function setup()
    {
        parent::setup();
        $this->type('password');
    }

    public function hydrate()
    {
    }
}
