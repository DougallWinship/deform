<?php

declare(strict_types=1);

namespace Deform\Component;

use Deform\Html\Html as Html;
use Deform\Html\IHtml;

class Input extends BaseComponent
{
    /** @var IHtml */
    public $input;
    public function setup()
    {
        $this->input = Html::input([
            'type' => 'text',
            'id' => $this->getId(),
            'name' => $this->getName(),
            'autocomplete' => 'off'
        ]);
        $this->control($this->input);
    }

    public function type($type): Input
    {
        $this->input->set('type', $type);
        return $this;
    }

    public function value($value): Input
    {
        $this->input->set('value', $value);
        return $this;
    }
}
