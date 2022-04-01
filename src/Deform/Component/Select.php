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
     * @inheritDoc
     */
    public function setValue($value): self
    {
        if (is_array($value)) {
            throw new \Exception("Select component can only set a single value");
        }
        $checkOptionTags = [];
        if ($this->hasOptGroups) {
            foreach ($this->select->getChildren() as $selectOptionGroup) {
                $checkOptionTags = array_merge($checkOptionTags, $selectOptionGroup->getChildren());
            }
        } else {
            $checkOptionTags = $this->select->getChildren();
        }
        foreach ($checkOptionTags as $checkOptionTag) {
            if ($checkOptionTag->get('value') === $value) {
                $checkOptionTag->set('selected', 'selected');
            } else {
                $checkOptionTag->unset('selected');
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

    /**
     * @inheritDoc
     */
    public function getHtmlTag(): HtmlTag
    {
        if (count($this->options) == 0) {
            throw new \Exception("A select component must contain at least one option");
        }
        return parent::getHtmlTag();
    }
}
