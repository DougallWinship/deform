<?php

declare(strict_types=1);

namespace Deform\Component;

use Deform\Html\Html as Html;
use Deform\Html\HtmlTag;

/**
 * @persistAttribute radioButtons
 */
class RadioButtonSet extends BaseComponent
{
    use Shadow\RadioButtonSet;

    /** @var array radio button values */
    private array $radioButtons;

    /** @var HtmlTag[] */
    private array $radioButtonInputsByValue = [];

    /**
     * @inheritDoc
     */
    public function setup(): void
    {
        // unusually don't do any setup here as it's only possible to do once the buttons have been specified
    }

    /**
     * @templateMethod
     * @param array $radioButtons
     * @return static
     * @throws \Exception
     */
    public function radioButtons(array $radioButtons): static
    {
        $isAssoc = \Deform\Util\Arrays::isAssoc($radioButtons);
        $this->radioButtons = $radioButtons;
        foreach ($radioButtons as $key => $value) {
            $radioButtonContainer = Html::div(['class' => 'radiobuttonset-radio-container']);
            $radioLabel = $value;
            $radioValue = $isAssoc ? $key : $value;
            $id = self::getMultiControlId($this->getId(), $radioValue);
            $radioButtonInput = Html::input([
                'type' => 'radio',
                'value' => $radioValue,
                'id' => $id,
                'name' => $this->getName(),
            ]);
            $this->radioButtonInputsByValue[$radioValue] = $radioButtonInput;
            $radioButtonContainer->add($radioButtonInput);
            $radioButtonContainer->add(" ");
            $radioButtonContainer->add(Html::label(['for' => $id, 'class' => 'multi-label'])->add($radioLabel));
            $this->addControl($radioButtonInput, $radioButtonContainer);
        }
        $this->addExpectedField($this->fieldName);
        return $this;
    }

    /**
     * @return static
     */
    public function clearSelected(): static
    {
        foreach ($this->radioButtonInputsByValue as $html) {
            $html->unset('checked');
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setValue($value): static
    {
        foreach ($this->radioButtonInputsByValue as $tagValue => $htmlTag) {
            if ($tagValue === $value) {
                $htmlTag->set('checked', true);
            } else {
                $htmlTag->unset('checked');
            }
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function hydrate(): void
    {
        if (count($this->radioButtons) > 0) {
            $this->radioButtons($this->radioButtons);
        }
    }
}
