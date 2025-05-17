<?php
namespace Deform\Component;

class DecimalTest extends \Codeception\Test\Unit
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
        $name = 'de';
        $decimal = ComponentFactory::Decimal($namespace ,$name, ['foo'=>'bar']);

        $autolabel = $this->tester->getAttributeValue($decimal, 'autoLabel');
        $this->assertTrue($autolabel);

        $container = $this->tester->getAttributeValue($decimal, 'componentContainer');
        $this->assertInstanceOf(\Deform\Component\ComponentContainer::class, $container);
        $control = $this->tester->getAttributeValue($container, 'control');
        $this->assertInstanceOf(\Deform\Component\ComponentControls::class, $control);

        $expectedId = 'decimal-'.$namespace.'-'.$name;
        $this->tester->assertIsHtmlTag($decimal->input,'input',[
            'id' => $expectedId,
            'name' => $namespace.'['.$name.']',
            'type' => 'text',
        ]);
    }
}
