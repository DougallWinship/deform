<?php
namespace Deform\Component;

/**
 * Note that input type='date' uses the format as per the browser's locale, to use a different format you should do
 * so manually using a standard Input
 */
class Date extends Input
{
    public function setup()
    {
        parent::setup();
        $this->type('date');
    }
}
