<?php

declare(strict_types=1);

namespace Deform\Component;

class Submit extends Input
{
    use Shadow\Submit;

    /**
     * @inheritDoc
     */
    public function setup(): void
    {
        parent::setup();
        $this->autolabel(false);
        $this->type('submit');
        $this->value($this->fieldName);
        $this->componentContainer->controlOnly = true;
    }
}
