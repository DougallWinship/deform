<?php

declare(strict_types=1);

namespace Deform\Component;

class SelectMulti extends Select
{
    /**
     * @inheritDoc
     */
    public function setup()
    {
        parent::setup();
        $this->select->set('multiple', 'multiple');
    }

    /**
     * @param array|string $value
     * @return $this
     * @throws \Exception
     */
    public function setSelected($value): self
    {
        $this->setSelectedForValues(is_array($value) ? $value : [$value]);
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return parent::getName() . "[]";
    }

    /**
     * @inheritDoc
     */
    public function setValue($value): self
    {
        foreach ($this->optionsHtml as $optionHtml) {
            $optionValue = $optionHtml->get('value');
            if (in_array($optionValue, $value)) {
                $optionHtml->set('selected', 'selected');
            } else {
                $optionHtml->unset('selected');
            }
        }
        return $this;
    }
}
