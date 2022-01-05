<?php
namespace Deform\Component;

class Display extends Input
{
    public function setup()
    {
        parent::setup();
        $this->input->set('disabled','disabled');
    }
}
