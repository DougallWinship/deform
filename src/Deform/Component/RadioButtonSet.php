<?php

declare(strict_types=1);

namespace Deform\Component;

use Deform\Html\Html as Html;
use Deform\Html\HtmlTag;

/**
 * @persistAttribute radioButtonInputsByValue
 */
class RadioButtonSet extends BaseComponent
{
    /** @var HtmlTag[] */
    private array $radioButtonInputsByValue = [];

    public function setup()
    {
    }

    /**
     * @param array $radioButtons
     * @return RadioButtonSet
     * @throws \Exception
     */
    public function radioButtons(array $radioButtons): self
    {
        $isAssoc = \Deform\Util\Arrays::isAssoc($radioButtons);
        foreach ($radioButtons as $key => $value) {
            $radioButtonContainer = Html::div(['class' => 'radio-button-container']);
            $radioValue = $isAssoc ? $key : $value;
            $id = $this->getId() . '-' . str_replace(' ', '_', $value);
            $radioButtonInput = Html::input([
                'type' => 'radio',
                'value' => $radioValue,
                'id' => $id,
                'name' => $this->getName()
            ]);
            $this->radioButtonInputsByValue[$radioValue] = $radioButtonInput;
            $radioButtonContainer->add($radioButtonInput);
            $radioButtonContainer->add(Html::label(['for' => $id])->add($value));
            $this->addControl($radioButtonContainer);
        }
        $expectedDataInput = Html::input([
            "type" => "hidden",
            "name" => $this->getExpectedDataName(),
            "value" => $this->fieldName
        ]);
        $this->addControl($expectedDataInput);
        return $this;
    }

    public function clearSelected()
    {
        foreach ($this->radioButtonInputsByValue as $html) {
            $html->unset('checked');
        }
        return $this;
    }

    public function setSelected($value)
    {
        if (!isset($this->radioButtonInputsByValue[$value])) {
            throw new \Exception("There is no radio button in the group with the value '" . $value . "'");
        }
        foreach ($this->radioButtonInputsByValue as $checkValue => $html) {
            if ($checkValue === $value) {
                $html->set('checked', 'checked');
            } else {
                $html->unset('checked');
            }
        }
        return $this;
    }
}
