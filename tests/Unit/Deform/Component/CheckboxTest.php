<?php
namespace App\Tests\Unit\Deform\Component;

use Deform\Component\ComponentFactory;

class CheckboxTest extends \Codeception\Test\Unit
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

    // tests
    public function testSetup()
    {
        $namespace = 'ns';
        $name = 'cb';
        $checkbox = ComponentFactory::Checkbox($namespace ,$name, ['foo'=>'bar']);

        $autolabel = $this->tester->getAttributeValue($checkbox, 'autoLabel');
        $this->assertTrue($autolabel);

        $expectedId = 'checkbox-'.$namespace.'-'.$name;
        $this->tester->assertIsHtmlTag($checkbox->input,'input',[
            'id' => $expectedId,
            'name' => $namespace.'['.$name.']',
            'type' => 'checkbox',
            'value' => 1
        ]);

        $container = $this->tester->getAttributeValue($checkbox, 'componentContainer');
        $this->assertInstanceOf(\Deform\Component\ComponentContainer::class, $container);

        $control = $this->tester->getAttributeValue($container, 'control');
        $this->assertInstanceOf(\Deform\Component\ComponentControls::class, $control);

        $inputLabel = $this->tester->getAttributeValue($checkbox, 'inputLabel');
        $this->tester->assertIsHtmlTag($inputLabel, 'label',['for'=>$expectedId]);
        $expectedInput = $this->tester->getAttributeValue($container, 'expectedInput');
        $this->tester->assertIsHtmlTag($expectedInput, 'input',[
            'type'=>'hidden',
            'name'=>$namespace.'[expected_data][]',
            'value'=>$name,
        ]);
    }

    public function testText()
    {
        $checkbox = ComponentFactory::Checkbox('namespace','checkbox', ['foo'=>'bar']);
        $labelText = "checkbox label";
        $checkbox->text($labelText);

        $value = $this->tester->getAttributeValue($checkbox, 'inputLabelText');
        $this->assertEquals($labelText, $value);

        $labelText = "new checkbox label";
        $checkbox->text($labelText);
        $value = $this->tester->getAttributeValue($checkbox, 'inputLabelText');
        $this->assertEquals($labelText, $value);
    }

    public function testSetValue()
    {
        $checkbox = ComponentFactory::Checkbox('namespace', 'checkbox', ['foo' => 'bar']);
        $inputTag = $this->tester->getAttributeValue($checkbox, 'input');
        $attributes = $this->tester->getAttributeValue($inputTag, 'attributes');
        $this->assertArrayNotHasKey('checked', $attributes);
        $checkbox->setValue(true);
        $attributes = $this->tester->getAttributeValue($inputTag, 'attributes');
        $this->assertArrayHasKey('checked', $attributes);
        $this->assertEquals('checked', $attributes['checked']);
        $checkbox->setValue(false);
        $attributes = $this->tester->getAttributeValue($inputTag, 'attributes');
        $this->assertArrayNotHasKey('checked', $attributes);
    }

    public function testHydrate()
    {
        $namespace = 'ns';
        $name = 'cb';
        $checkbox = ComponentFactory::Checkbox($namespace, $name, ['foo' => 'bar']);
        $labelText = 'checkbox label';
        $this->tester->setAttributeValue($checkbox, 'inputLabelText', $labelText);
        $checkbox->hydrate();

        $expectedId = 'checkbox-'.$namespace.'-'.$name;
        $this->tester->assertIsHtmlTag($checkbox->input,'input',[
            'id' => $expectedId,
            'name' => $namespace.'['.$name.']',
            'type' => 'checkbox',
            'value' => 1
        ]);

        $labelTextTag = $this->tester->getAttributeValue($checkbox, 'inputLabel');
        $this->tester->assertIsHtmlTag($labelTextTag, 'label', ['for'=>$expectedId]);
    }
}