<?php

declare(strict_types=1);

namespace Deform\Component;

use Deform\Html\Html as Html;
use Deform\Html\HtmlTag;
use Deform\Html\IHtml;

/**
 * @persistAttribute hasOptGroups
 * @persistAttribute options
 */
class Select extends BaseComponent
{
    /** @var HtmlTag */
    public IHtml $select;

    public bool $hasOptGroups = false;
    public array $options = [];

    /** @var HtmlTag[]*/
    public array $optionsHtml = [];

    /**
     * @inheritDoc
     */
    public function setup()
    {
        $this->select = Html::select([
            'id' => $this->getId(),
            'name' => $this->getName(),
        ]);
        $this->addControl($this->select);
    }

    /**
     * @param array $options
     * @return $this
     * @throws \Exception
     */
    public function options(array $options): self
    {
        if ($this->options != $options) {
            $this->options = $options;
            $this->hasOptGroups = false;
        }
        $this->select->clear();
        $isAssoc = \Deform\Util\Arrays::isAssoc($options);
        foreach ($this->options as $key => $value) {
            $option = Html::option(['value' => $isAssoc ? $key : $value])->add($value);
            $this->select->add($option);
            $this->optionsHtml[] = $option;
        }
        return $this;
    }

    /**
     * @param array $optgroupOptions
     * @return $this
     * @throws \Exception
     */
    public function optgroupOptions(array $optgroupOptions): self
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
                $option = Html::option(['value' => $isAssoc ? $key : $value])->add($value);
                $optgroup->add($option);
                $this->optionsHtml[] = $option;
            }
            $this->select->add($optgroup);
        }
        return $this;
    }

    /**
     * @param string|array $value
     * @return $this
     * @throws \Exception
     */
    public function setSelected($value): self
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

    /**
     * @inheritDoc
     */
    public function setValue($value): self
    {
        foreach ($this->optionsHtml as $optionHtml) {
            $optionValue = $optionHtml->get('value');
            if ($value === $optionValue) {
                $optionHtml->set('selected', 'selected');
            } else {
                $optionHtml->unset('selected');
            }
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function hydrate()
    {
        if ($this->hasOptGroups) {
            $this->optgroupOptions($this->options);
        } else {
            $this->options($this->options);
        }
    }
}
