<?php

declare(strict_types=1);

namespace Deform\Component;

use Deform\Html\Html as Html;
use Deform\Html\IHtml;

/**
 * @persistAttribute hasOptGroups
 * @persistAttribute options
 */
class Select extends BaseComponent
{
    /** @var IHtml */
    public IHtml $select;
    public bool $hasOptGroups = false;
    public array $options = [];

    public function setup()
    {
        $this->select = Html::select([
            'id' => $this->getId(),
            'name' => $this->getName(),
        ]);
        $this->addControl($this->select);
    }

    public function options($options): Select
    {
        if ($this->options != $options) {
            $this->options = $options;
            $this->hasOptGroups = false;
        }
        $this->select->clear();
        $isAssoc = \Deform\Util\Arrays::isAssoc($options);
        foreach ($this->options as $key => $value) {
            $this->select->add(Html::option(['value' => $isAssoc ? $key : $value])->add($value));
        }
        return $this;
    }

    public function optgroupOptions(array $optgroupOptions): Select
    {
        if ($this->options != $optgroupOptions) {
            $this->options = $optgroupOptions;
            $this->hasOptGroups = true;
        }
        $this->select->clear();

        foreach ($optgroupOptions as $groupName => $options) {
            $optgroup = Html::optgroup(['label' => $groupName]);
            $isAssoc = \Deform\Util\Arrays::isAssoc($options);
            foreach ($options as $key => $value) {
                $optgroup->add(Html::option(['value' => $isAssoc ? $key : $value])->add($value));
            }
            $this->select->add($optgroup);
        }
        return $this;
    }

    /**
     * @param string|array $value
     * @return Select
     * @throws \Exception
     */
    public function setSelected($value): Select
    {
        if (is_array($value)) {
            throw new \Exception("Only one option can be selected for a non-multi select");
        }
        $this->setSelectedForValues([$value]);
        return $this;
    }

    /**
     * @param array $valueArray
     * @throws \Exception
     */
    protected function setSelectedForValues(array $valueArray)
    {
        foreach ($this->select->getChildren() as $child) {
            if ($child->getTagType() == 'optgroup') {
                foreach ($child->getChildren() as $optgroupChild) {
                    if (in_array($optgroupChild->get('value'), $valueArray)) {
                        $optgroupChild->set('selected', 'selected');
                    } else {
                        $child->unset('selected');
                    }
                }
            } else {
                $check = $child->get('value');
                if (in_array($check, $valueArray)) {
                    $child->set('selected', 'selected');
                } else {
                    $child->unset('selected');
                }
            }
        }
    }

    public function hydrate()
    {
        if ($this->hasOptGroups) {
            $this->optgroupOptions($this->options);
        } else {
            $this->options($this->options);
        }
    }
}
