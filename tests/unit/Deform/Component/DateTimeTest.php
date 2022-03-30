<?php
namespace Deform\Component;

class DateTimeTest extends \Codeception\Test\Unit
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
        $name = 'ddt';
        $datetime = ComponentFactory::DateTime($namespace ,$name, ['foo'=>'bar']);

        $autolabel = $this->tester->getAttributeValue($datetime, 'autoLabel');
        $this->assertTrue($autolabel);

        $container = $this->tester->getAttributeValue($datetime, 'componentContainer');
        $this->assertInstanceOf(\Deform\Component\ComponentContainer::class, $container);
        $control = $this->tester->getAttributeValue($container, 'control');
        $this->assertInstanceOf(\Deform\Component\ComponentControls::class, $control);

        $expectedId = 'datetime-'.$namespace.'-'.$name;
        $this->tester->assertIsHtmlTag($datetime->input,'input',[
            'id' => $expectedId,
            'name' => $namespace.'['.$name.']',
            'type' => 'datetime-local',
        ]);
    }
}
