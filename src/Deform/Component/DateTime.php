<?php

declare(strict_types=1);

namespace Deform\Component;

class DateTime extends Input
{
    use Shadow\Input;

    /**
     * @inheritDoc
     */
    public function setup()
    {
        parent::setup();
        $this->type('datetime-local');
    }

    public function hydrate()
    {
    }
}
