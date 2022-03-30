<?php
namespace Deform\Component;

class InputButtonTest extends \Codeception\Test\Unit
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

    public function testSetup()
    {
        $namespace = 'ns';
        $name = 'dp';
        $inputButton = ComponentFactory::InputButton($namespace ,$name, ['foo'=>'bar']);

        $autolabel = $this->tester->getAttributeValue($inputButton, 'autoLabel');
        $this->assertTrue($autolabel);

        $container = $this->tester->getAttributeValue($inputButton, 'componentContainer');
        $this->assertInstanceOf(\Deform\Component\ComponentContainer::class, $container);
        $control = $this->tester->getAttributeValue($container, 'control');
        $this->assertInstanceOf(\Deform\Component\ComponentControls::class, $control);

        $expectedId = 'inputbutton-'.$namespace.'-'.$name;
        $this->tester->assertIsHtmlTag($inputButton->input,'input',[
            'id' => $expectedId,
            'name' => $namespace.'['.$name.']',
            'type' => 'button',
        ]);
    }
}
