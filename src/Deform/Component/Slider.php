<?php

declare(strict_types=1);

namespace Deform\Component;

/**
 * @method $this min(int $min)
 * @method $this max(int $max)
 * @method $this step(mixed $step) usually an int a float or 'any'
 */
class Slider extends Input
{
    /**
     * @inheritDoc
     */
    public function setup()
    {
        parent::setup();
        $this->input->type('range');
    }

    public function hydrate()
    {
    }
}
