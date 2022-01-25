<?php

declare(strict_types=1);

namespace Deform\Component;

/**
 */
class MultipleEmail extends Email
{
    public function setup()
    {
        parent::setup();
        $this->input->set('multiple', 'multiple');
    }
}
