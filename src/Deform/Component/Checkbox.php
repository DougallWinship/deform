<?php

declare(strict_types=1);

namespace Deform\Component;

use Deform\Html\Html as Html;
use Deform\Html\IHtml;

/**
 * @method Checkbox checked(string $checked)
 * @persistAttribute inputLabelText
 */
class Checkbox extends Input
{
    public string $inputLabelText;
    public $inputLabel;

    /**
     * @throws \Exception
     */
    public function setup()
    {
        parent::setup();
        $this->type('checkbox');
        $this->value(1);
        $this->inputLabel = Html::label(['for' => $this->getId()])->add($this->fieldName);
        $expectedDataInput = Html::input([
            "type" => "hidden",
            "name" => $this->getExpectedDataName(),
            "value" => $this->fieldName
        ]);
        $this->control($this->input, [
            $this->input,
            ' ',
            $this->inputLabel,
            $expectedDataInput,
        ]);
        $this->componentContainer->disableLabel = true;
    }

    public function text($text): Checkbox
    {
        $this->inputLabelText = $text;
        $this->inputLabel->reset($text);
        return $this;
    }
}
