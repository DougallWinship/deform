<?php
namespace Deform\Component;

class MultipleFileTest extends \Codeception\Test\Unit
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
        $multipleFile = ComponentFactory::MultipleFile($namespace ,$name, ['foo'=>'bar']);

        $autolabel = $this->tester->getAttributeValue($multipleFile, 'autoLabel');
        $this->assertTrue($autolabel);

        $container = $this->tester->getAttributeValue($multipleFile, 'componentContainer');
        $this->assertInstanceOf(\Deform\Component\ComponentContainer::class, $container);
        $control = $this->tester->getAttributeValue($container, 'control');
        $this->assertInstanceOf(\Deform\Component\ComponentControls::class, $control);

        $expectedId = 'multiplefile-'.$namespace.'-'.$name;
        $this->tester->assertIsHtmlTag($multipleFile->input,'input',[
            'id' => $expectedId,
            'name' => $namespace.'['.$name.'][]',
            'type' => 'file',
            'multiple' => 'multiple'
        ]);
    }
}
