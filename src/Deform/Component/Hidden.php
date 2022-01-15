<?php

declare(strict_types=1);

namespace Deform\Component;

class Hidden extends Input
{
    public function setup()
    {
        parent::setup();
        $this->componentContainer->controlOnly = true;
        $this->type('hidden');
    }
}
