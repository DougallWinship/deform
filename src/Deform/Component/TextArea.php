<?php
namespace Deform\Component;;

use Deform\Html\Html as Html;

class TextArea extends BaseComponent
{
    public $textarea;

    public function setup()
    {
        $this->textarea = Html::textarea([
           'id' => $this->getId(),
           'name' => $this->getName()
        ]);

        $this->control($this->textarea);
    }
}
