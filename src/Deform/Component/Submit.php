<?php

declare(strict_types=1);

namespace Deform\Component;

class Submit extends Input
{
    public function setup()
    {
        parent::setup();
        $this->autolabel(false);
        $this->type('submit');
        $this->value($this->fieldName);
        $this->componentContainer->controlOnly = true;
    }
}
