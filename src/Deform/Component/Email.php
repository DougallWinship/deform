<?php

declare(strict_types=1);

namespace Deform\Component;

class Email extends Input
{
    /**
     * @inheritDoc
     */
    public function setup()
    {
        parent::setup();
        $this->type('email');
    }

    public function hydrate()
    {
    }
}
