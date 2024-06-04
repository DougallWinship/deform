<?php

declare(strict_types=1);

namespace Deform\Component;

use Deform\Html\Html as Html;
use Deform\Html\HtmlTag;
use Deform\Html\IHtml;

/**
 * @persistAttribute hasOptGroups
 * @persistAttribute optionsValues
 */
class Select extends BaseComponent
{
    use Shadow\Select;

    /** @var HtmlTag */
    public IHtml $select;

    public bool $hasOptGroups = false;

    public array $options = [];
    public ?array $optionsValues = null;
    /**
     * @inheritDoc
     */
    public function setup(): void
    {
        $this->select = Html::select([
            'id' => $this->getId(),
            'name' => $this->getName(),
        ]);
        $this->addControl($this->select);
    }

    /**
     * @templateMethod
     * @param array $optionsValues
     * @return static
     * @throws \Exception
     */
    public function options(array $optionsValues): static
    {
        if ($this->optionsValues !== $optionsValues) {
            $this->optionsValues = $optionsValues;
            $this->hasOptGroups = false;
        }
        $this->options = [];
        $this->select->clear();
        $isAssoc = \Deform\Util\Arrays::isAssoc($optionsValues);
        foreach ($this->optionsValues as $key => $value) {
            $option = Html::option(['value' => $isAssoc ? $key : $value])->add($value);
            $this->select->add($option);
            $this->options[] = $option;
        }
        return $this;
    }

    /**
     * @param array $optgroupOptions
     * @return static
     * @throws \Exception
     */
    public function optgroupOptions(array $optgroupOptions): static
    {
        if ($this->optionsValues != $optgroupOptions) {
            $this->optionsValues = $optgroupOptions;
            $this->hasOptGroups = true;
        }
        $this->select->clear();

        foreach ($optgroupOptions as $groupName => $options) {
            $optgroup = Html::optgroup(['label' => $groupName]);
            $isAssoc = \Deform\Util\Arrays::isAssoc($options);
            foreach ($options as $key => $value) {
                $option = Html::option(['value' => $isAssoc ? $key : $value])->add($value);
                $optgroup->add($option);
                $this->options[] = $option;
            }
            $this->select->add($optgroup);
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setValue($value): static
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
    public function hydrate(): void
    {
        if ($this->hasOptGroups) {
            $this->optgroupOptions($this->optionsValues);
        } else {
            $this->options($this->optionsValues);
        }
    }

    /**
     * @inheritDoc
     */
    public function getHtmlTag(): HtmlTag
    {
        if (!is_array($this->optionsValues)) {
            throw new \Exception("Select component options must be an array");
        }
        if (count($this->optionsValues) == 0) {
            throw new \Exception("A select component must contain at least one option");
        }
        return parent::getHtmlTag();
    }
}
