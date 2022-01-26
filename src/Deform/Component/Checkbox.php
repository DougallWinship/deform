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
    public ?string $inputLabelText = null;
    public $inputLabel;

    /**
     * @throws \Exception
     */
    public function setup()
    {
        $this->autoAddControl = false;
        parent::setup();
        $this->type('checkbox');
        $this->value(1);
        $this->inputLabel = Html::label(['for' => $this->getId()])->add($this->fieldName);
        $this->addControl($this->input, [
            $this->input,
            ' ',
            $this->inputLabel,
        ]);
        $this->componentContainer->disableLabel = true;
        $this->addExpectedField($this->fieldName);
    }

    public function text($text): Checkbox
    {
        $this->inputLabelText = $text;
        $this->inputLabel->reset($text);
        return $this;
    }

    public function hydrate()
    {
        if (is_string($this->inputLabelText)) {
            $this->text($this->inputLabelText);
        }
    }
}
