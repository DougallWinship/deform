<?php

declare(strict_types=1);

namespace Deform\Component;

class Hidden extends Input
{
    use Shadow\Hidden;

    /**
     * @inheritDoc
     */
    public function setup(): void
    {
        parent::setup();
        $this->autolabel(false);
        $this->type('hidden');
        $this->componentContainer->controlOnly = true;
    }
}
