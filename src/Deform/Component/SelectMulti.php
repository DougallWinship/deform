<?php

declare(strict_types=1);

namespace Deform\Component;

/**
 * @persistAttribute hasOptGroups
 * @persistAttribute optionsValues
 */
class SelectMulti extends Select
{
    use Shadow\SelectMulti;

    /**
     * @inheritDoc
     */
    public function setup(): void
    {
        parent::setup();
        $this->select->set('multiple', 'multiple');
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
    public function setValue($value): static
    {
        if (is_string($value)) {
            return parent::setValue($value);
        } elseif (is_array($value)) {
            if ($this->hasOptGroups) {
                $checkOptionTags = [];
                foreach ($this->select->getChildren() as $selectOptionGroup) {
                    $checkOptionTags = array_merge($checkOptionTags, $selectOptionGroup->getChildren());
                }
            } else {
                $checkOptionTags = $this->select->getChildren();
            }
            foreach ($checkOptionTags as $optionTag) {
                if (in_array($optionTag->get('value'), $value)) {
                    $optionTag->set('selected', 'selected');
                } else {
                    $optionTag->unset('selected');
                }
            }
        }
        return $this;
    }
}
