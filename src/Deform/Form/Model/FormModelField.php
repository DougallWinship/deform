<?php

namespace Deform\Form\Model;

class FormModelField
{
    public $name;
    public $componentDefinition;
    public $validators;

    public function __construct($name, $componentDefinition, $validators=[])
    {
        $this->name = $name;
        $this->componentDefinition = $componentDefinition;
        $this->validators = $validators;
    }
}
