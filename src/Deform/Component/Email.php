<?php

declare(strict_types=1);

namespace Deform\Component;

class Email extends Input
{
    public function setup()
    {
        parent::setup();
        $this->type('email');
    }
}
