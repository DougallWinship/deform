<?php
namespace Deform\Form\Model;

use Deform\Component\ComponentFactory as Component;

class ExampleFormModel extends FormModel
{
    public function __construct()
    {
        parent::__construct('login-form-example','POST');
        $this->addEmail('email');
        $this->addPassword('login');
        $this->addSubmit('submit');
    }

}
