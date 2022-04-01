<?php

declare(strict_types=1);

namespace Deform\Component;

class Hidden extends Input
{
    /**
     * @inheritDoc
     */
    public function setup()
    {
        parent::setup();
        $this->componentContainer->controlOnly = true;
        $this->autolabel(false);
        $this->type('hidden');
    }

    public function hydrate()
    {
    }
}
