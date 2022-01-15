<?php

declare(strict_types=1);

namespace Deform\Component;

class SelectMulti extends Select
{
    public function setup()
    {
        parent::setup();
        $this->select->set('multiple', 'multiple');
    }

    public function setSelected($value): Select
    {
        $this->setSelectedForValues(is_array($value) ? $value : [$value]);
        return $this;
    }

    public function getName(): string
    {
        return parent::getName() . "[]";
    }
}
