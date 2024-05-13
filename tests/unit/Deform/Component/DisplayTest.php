<?php
namespace Deform\Component;

class DisplayTest extends \Codeception\Test\Unit
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
        $display = ComponentFactory::Display($namespace ,$name, ['foo'=>'bar']);

        $autolabel = $this->tester->getAttributeValue($display, 'autoLabel');
        $this->assertTrue($autolabel);

        $container = $this->tester->getAttributeValue($display, 'componentContainer');
        $this->assertInstanceOf(\Deform\Component\ComponentContainer::class, $container);
        $control = $this->tester->getAttributeValue($container, 'control');
        $this->assertInstanceOf(\Deform\Component\ComponentControls::class, $control);

        $expectedId = 'display-'.$namespace.'-'.$name;
        $this->tester->assertIsHtmlTag($display->input,'input',[
            'id' => $expectedId,
            'name' => $namespace.'['.$name.']',
            'type' => 'text',
            'disabled' => 'disabled'
        ]);
    }


    public function testHydrate()
    {
        $namespace = 'ns';
        $name = 'dp';
        $display = ComponentFactory::Display($namespace ,$name, ['foo'=>'bar']);
        $display->hydrate();
        $expectedId = 'display-'.$namespace.'-'.$name;
        $this->tester->assertIsHtmlTag($display->input,'input',[
            'id' => $expectedId,
            'name' => $namespace.'['.$name.']',
            'type' => 'text',
            'disabled' => 'disabled'
        ]);
    }
}
