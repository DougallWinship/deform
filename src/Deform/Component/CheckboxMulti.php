<?php

declare(strict_types=1);

namespace Deform\Component;

use Deform\Html\Html as Html;
use Deform\Util\Arrays;

/**
 * @persistAttribute checkboxesValues
 */
class CheckboxMulti extends BaseComponent
{
    public array $checkboxes = [];
    private array $checkboxValues;

    public function setup()
    {
    }

    public function checkboxes(array $checkboxes): CheckboxMulti
    {
        $this->checkboxValues = $checkboxes;
        $this->checkboxes = [];
        $isAssoc = Arrays::isAssoc($checkboxes);
        $name = $this->getName() . "[]";
        $id = $this->getId();
        foreach ($checkboxes as $key => $value) {
            $wrapper = Html::div(['class' => 'checkbox-wrapper']);
            $checkboxId = $id . '-' . $key;
            $checkbox = Html::input([
                'type' => 'checkbox',
                'id' => $checkboxId,
                'name' => $name,
                'value' => ($isAssoc ? $key : $value)
            ]);
            $this->checkboxes[] = $checkbox;
            $wrapper->add($checkbox)
                    ->add(" ")
                    ->add(Html::label(['for' => $checkboxId])->add($value));
            $this->addControl($wrapper);
        }
        // special hidden field to indicate all checkbox field for which data is to be expected
        // this is necessary since unchecked fields will not send a -ve in the data (they are simply not there!)
        $expectedDataInput = Html::input([
            "type" => "hidden",
            "name" => $this->getExpectedDataName(),
            "value" => $this->fieldName
        ]);
        // labels are already generated for each of the checkboxes
        $this->autoLabel(false);
        // this is an example of multi control
        $this->addControl($expectedDataInput);
        return $this;
    }
}
