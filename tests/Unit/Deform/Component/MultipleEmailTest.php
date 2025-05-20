<?php
namespace App\Tests\Unit\Deform\Component;

use Deform\Component\ComponentFactory;

class MultipleEmailTest extends \Codeception\Test\Unit
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
        $multipleEmail = ComponentFactory::MultipleEmail($namespace ,$name, ['foo'=>'bar']);

        $autolabel = $this->tester->getAttributeValue($multipleEmail, 'autoLabel');
        $this->assertTrue($autolabel);

        $container = $this->tester->getAttributeValue($multipleEmail, 'componentContainer');
        $this->assertInstanceOf(\Deform\Component\ComponentContainer::class, $container);
        $control = $this->tester->getAttributeValue($container, 'control');
        $this->assertInstanceOf(\Deform\Component\ComponentControls::class, $control);

        $expectedId = 'multipleemail-'.$namespace.'-'.$name;
        $this->tester->assertIsHtmlTag($multipleEmail->input,'input',[
            'id' => $expectedId,
            'name' => $namespace.'['.$name.']',
            'type' => 'email',
            'multiple' => 'multiple'
        ]);
    }
}
