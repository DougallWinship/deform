<?php
namespace unit\Deform\Component;

use Deform\Component\ComponentFactory;

class IntegerTest extends \Codeception\Test\Unit
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
        $name = 'in';
        $integer = ComponentFactory::Integer($namespace ,$name, ['foo'=>'bar']);

        $autolabel = $this->tester->getAttributeValue($integer, 'autoLabel');
        $this->assertTrue($autolabel);

        $container = $this->tester->getAttributeValue($integer, 'componentContainer');
        $this->assertInstanceOf(\Deform\Component\ComponentContainer::class, $container);
        $control = $this->tester->getAttributeValue($container, 'control');
        $this->assertInstanceOf(\Deform\Component\ComponentControls::class, $control);

        $expectedId = 'integer-'.$namespace.'-'.$name;
        $this->tester->assertIsHtmlTag($integer->input,'input',[
            'id' => $expectedId,
            'name' => $namespace.'['.$name.']',
            'type' => 'number',
        ]);
    }
}
