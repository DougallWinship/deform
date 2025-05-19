<?php

declare(strict_types=1);

namespace App;

use Deform\Form\FormModel;

class ExampleFormModel extends FormModel
{
    /**
     * @throws \Exception
     */
    public function __construct()
    {
        parent::__construct('example');
        $this->addHtml("<h1>Example Form Model</h1>");
        $this->addCheckbox('cb')->label("checkbox")->text("checkbox label text");
        $this->addCheckboxMulti('cbm')->checkboxes(['True','False','File not Found'])->label("checkboxes");
        $this->addColorSelector('cs')->label("colour");
        $this->addCurrency('cu')->currency("&pound;")->label("currency");
        $this->addDate('dt')->label("date");
        $this->addDateTime('ddt')->label("datetime");
        $this->addDecimal('de')->label("decimal");
        $this->addEmail('eml')->label("email")->autocomplete('off');
        $this->addFile('fl')->accept("txt")->label("file");
        $this->addImage('im')->label("image");
        $this->addInteger('in')->label("integer");
        $this->addPassword('password')->autocomplete("off")->label('password')->minlength(8)->maxlength(16);
        $this->addRadioButtonSet('rbs')->radioButtons(['four','five','six'])->label("radio buttons");
        $this->addSelect('sl')->options(['one','two','three'])->hint('whatevs')->label('select');
        $this->addSelectMulti('slm')->options(['seven', 'eight', 'nine'])->label('select multi');
        $this->addSlider('sd')->label('slider')->min(1)->max(101)->step(2)->showOutput(true);
        $this->addText('tx')->label('text')->minlength(8)->maxlength(16);
        $this->addText('txdl')->label('text datalist')->datalist(['one','two','three','four','five','six']);
        $this->addTextArea('ta')->label("textarea");
        $this->addSubmit('submit')->wrap('div',['class' => 'center']);
    }
}
