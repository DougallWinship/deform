<?php

declare(strict_types=1);

namespace Deform\Component;

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
        $this->addControl($this->textarea);
    }
}
