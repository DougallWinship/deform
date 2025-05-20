<?php
namespace App\Tests\Unit\Deform\Component;

use Deform\Component\ComponentFactory;

class HiddenTest extends \Codeception\Test\Unit
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
        $hidden = ComponentFactory::Hidden($namespace ,$name, ['foo'=>'bar']);

        $autolabel = $this->tester->getAttributeValue($hidden, 'autoLabel');
        $this->assertFalse($autolabel);

        $container = $this->tester->getAttributeValue($hidden, 'componentContainer');
        $this->assertInstanceOf(\Deform\Component\ComponentContainer::class, $container);
        $control = $this->tester->getAttributeValue($container, 'control');
        $this->assertInstanceOf(\Deform\Component\ComponentControls::class, $control);
        $controlOnly = $this->tester->getAttributeValue($container, 'controlOnly');
        $this->assertTrue($controlOnly);

        $expectedId = 'hidden-'.$namespace.'-'.$name;
        $this->tester->assertIsHtmlTag($hidden->input,'input',[
            'id' => $expectedId,
            'name' => $namespace.'['.$name.']',
            'type' => 'hidden',
        ]);
    }
}
