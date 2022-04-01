<?php

declare(strict_types=1);

namespace Deform\Component;

class Display extends Input
{
    /**
     * @inheritDoc
     */
    public function setup()
    {
        parent::setup();
        $this->input->set('disabled', 'disabled');
        $this->input->type('text');
    }

    public function hydrate()
    {
    }
}
