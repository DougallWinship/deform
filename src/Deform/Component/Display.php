<?php

declare(strict_types=1);

namespace Deform\Component;

class Display extends Input
{
    use Shadow\Input;

    /**
     * @inheritDoc
     */
    public function setup(): void
    {
        parent::setup();
        $this->input->set('disabled', 'disabled');
        $this->input->type('text');
    }

    public function hydrate()
    {
    }
}
