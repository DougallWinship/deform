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

    /**
     * @param array|string $value
     *
     * @return SelectMulti
     */
    public function setSelected($value): Select
    {
        if (!is_array($value)) {
            $value = [$value];
        }
        $this->setSelectedForValues($value);
        return $this;
    }

    public function getName(): string
    {
        return parent::getName() . "[]";
    }
}
