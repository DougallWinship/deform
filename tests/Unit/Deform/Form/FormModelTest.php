<?php

namespace App\Tests\Unit\Deform\Form;

use Deform\Exception\DeformException;
use Deform\Form\FormModel;

class FormModelTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    public function testBadMethodConstructor()
    {
        $this->expectException(DeformException::class);
        new FormModel('ns', 'bad-form-method');
    }

    public function testConstructor()
    {
        $exampleForm = new \App\ExampleFormModel();
        $exampleForm->setCSRFStrategy(FormModel::CSRF_STRATEGY_OFF);
        $this->assertInstanceOf(FormModel::class, $exampleForm);
        $formHtml = $exampleForm->getFormHtml();
        $this->tester->assertIsHtmlTag($formHtml,'form', [
            'method' => 'post',
            'action' => '',
            'autocomplete' => 'off',
            'enctype' => 'multipart/form-data'
        ]);
    }
}