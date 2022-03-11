<?php

declare(strict_types=1);

namespace Deform\Form\Model;

class ExampleFormModel extends FormModel
{
    /**
     * @throws \Exception
     */
    public function __construct()
    {
        parent::__construct('example');
        $this->addCheckbox('cb')->label("Yes or No");
        $this->addCheckboxMulti('cbm')->checkboxes(['True','False','File not Found'])->label("Yes or No");
        $this->addCurrency('cu')->currency("&pound;")->label("how much?");
        $this->addDate('dt')->label("when?");
        $this->addDateTime('ddt')->label("when?");
        $this->addEmail('eml')->autocomplete('off');
        $this->addFile('fl')->label("File");
        $this->addPassword('password')->autocomplete("off");
        $this->addRadioButtonSet('rbs')->radioButtons(['four','five','six']);
        $this->addSelect('sl')->options(['one','two','three'])->hint('whatevs');
        $this->addSelectMulti('slm')->options(['seven', 'eight', 'nine'])->label('multi');
        $this->addSlider('sd')->label('slides');
        $this->addText('tx')->label('Text');
        $this->addTextArea('ta')->label("lots of text");
        $this->addSubmit('submit');
    }

    public function processFormData($formData)
    {
        return true;
    }
}
