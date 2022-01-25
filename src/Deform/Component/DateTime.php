<?php

declare(strict_types=1);

namespace Deform\Component;

class DateTime extends Input
{
    public function setup()
    {
        parent::setup();
        $this->type('datetime-local');
    }
}
