<?php
namespace Deform\Component;

use Deform\Html\Html as Html;
use Deform\Html\IHtml;

/**
 * @method IHtml label(string $text)
 * @method Checkbox inputCheckbox(array $options)
 * @method IHtml errorDiv(array $options)
 * @method IHtml component_content_div(array $options)
 */
class Checkbox extends Input
{
    public $inputLabel;

    public function setup()
    {
        parent::setup();
        $this->type('checkbox');
        $this->value(1);
        $this->inputLabel = Html::label(['for'=>$this->getId()])->add($this->fieldName);
        $expectedDataInput = Html::input(["type" => "hidden", "name" => $this->getExpectedDataName(), "value" => $this->fieldName]);

        $this->control([
            $this->input,
            ' ',
            $this->inputLabel,
            $expectedDataInput,
        ]);
        $this->componentContainer->disableLabel=true;
    }

    public function text($text): Checkbox
    {
        $this->inputLabel->reset($text);
        return $this;
    }

    public function beforeRender()
    {
        $this->componentContainer->labelTag=false;
//        if ($this->inputLabel->isEmpty()) {
//            $this->inputLabel->add($this->fieldName);
//        }
    }
}
