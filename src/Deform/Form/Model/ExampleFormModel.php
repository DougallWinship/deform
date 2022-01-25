<?php

declare(strict_types=1);

namespace Deform\Form\Model;

use Deform\Component\ComponentFactory as Component;

class ExampleFormModel extends FormModel
{
    public function __construct()
    {
        parent::__construct('login-form-example');
        $this->addEmail('email')->disabled('disabled');
        $this->addPassword('login');
        $this->addHtml("<div><div>wibble</div></div>");
        $this->addSubmit('submit');
        $this->addSelect('something')->options(['one','two','three'])->hint('whatevs');
    }
}
