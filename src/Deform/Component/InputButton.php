<?php

declare(strict_types=1);

namespace Deform\Component;

class InputButton extends Input
{
    public function setup()
    {
        parent::setup();
        $this->type('button');
    }

    public function beforeRender()
    {
        $this->input->setIfEmpty('value', $this->fieldName);
    }
}
