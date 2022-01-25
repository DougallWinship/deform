<?php

declare(strict_types=1);

namespace Deform\Component;

/**
 * @method self accept(string $acceptType)
 */
class MultipleFile extends File
{
    public function setup()
    {
        parent::setup();
        $this->input->set('multiple', 'multiple');
    }
}
