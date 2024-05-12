<?php

declare(strict_types=1);

namespace Deform\Component;

class Email extends Input
{
    use Shadow\Input;

    /**
     * @inheritDoc
     */
    public function setup(): void
    {
        parent::setup();
        $this->type('email');
    }

    public function hydrate()
    {
    }
}
