<?php
namespace Deform\Component;

class DateTest extends \Codeception\Test\Unit
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
        $name = 'dt';
        $date = ComponentFactory::Date($namespace ,$name, ['foo'=>'bar']);

        $autolabel = $this->tester->getAttributeValue($date, 'autoLabel');
        $this->assertTrue($autolabel);

        $container = $this->tester->getAttributeValue($date, 'componentContainer');
        $this->assertInstanceOf(\Deform\Component\ComponentContainer::class, $container);
        $control = $this->tester->getAttributeValue($container, 'control');
        $this->assertInstanceOf(\Deform\Component\ComponentControls::class, $control);


        $expectedId = 'date-'.$namespace.'-'.$name;
        $this->tester->assertIsHtmlTag($date->input,'input',[
            'id' => $expectedId,
            'name' => $namespace.'['.$name.']',
            'type' => 'date',
        ]);
    }

    public function testHydrate()
    {
        $namespace = 'ns';
        $name = 'dt';
        $date = ComponentFactory::Date($namespace ,$name, ['foo'=>'bar']);
        $this->assertNull($date->hydrate());
    }
}
