<?php

declare(strict_types=1);

namespace Deform\Component;

/**
 * Note that input type='date' uses the format as per the browser's locale, to use a different format you should
 * manually do so using a standard Input
 */
class Date extends Input
{
    /**
     * @inheritDoc
     */
    public function setup()
    {
        parent::setup();
        $this->type('date');
    }

    public function hydrate()
    {
    }
}
