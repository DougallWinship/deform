<?php
namespace Deform\Component;

class DateTime extends Input
{
    public $input;

    public function setup()
    {
        parent::setup();
        $this->type('datetime-local');
    }
}
