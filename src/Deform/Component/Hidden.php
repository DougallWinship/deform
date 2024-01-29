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
        $this->autolabel(false);
        $this->type('hidden');
        $this->componentContainer->controlOnly = true;
    }

    public function hydrate()
    {
    }
}
