<?php

namespace Deform\Form\Model;

class TestFormModel extends FormModel
{
    public function __construct()
    {
        $this->defineField(
            'name',
            \Deform\Component\Input::class
        );
        $this->addField(
            new FormModelField(
                'password',
                \Deform\Component\Password::class,
                [
                   //new \Deform\Form\Validation\ValidationRule()
                ]
            )
        );
    }
}
