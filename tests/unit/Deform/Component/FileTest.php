<?php
namespace Deform\Component;

class FileTest extends \Codeception\Test\Unit
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
        $file = ComponentFactory::File($namespace ,$name, ['foo'=>'bar']);

        $autolabel = $this->tester->getAttributeValue($file, 'autoLabel');
        $this->assertTrue($autolabel);

        $container = $this->tester->getAttributeValue($file, 'componentContainer');
        $this->assertInstanceOf(\Deform\Component\ComponentContainer::class, $container);
        $control = $this->tester->getAttributeValue($container, 'control');
        $this->assertInstanceOf(\Deform\Component\ComponentControls::class, $control);

        $expectedId = 'file-'.$namespace.'-'.$name;
        $this->tester->assertIsHtmlTag($file->input,'input',[
            'id' => $expectedId,
            'name' => $namespace.'['.$name.']',
            'type' => 'file',
        ]);
    }

    public function testSetAccept()
    {
        $namespace = 'ns';
        $name = 'dp';
        $accept = 'file/*';
        $file = ComponentFactory::File($namespace ,$name, ['foo'=>'bar'])
            ->accept($accept);

        $attributes = $this->tester->getAttributeValue($file->input, 'attributes');
        $this->assertArrayHasKey('accept', $attributes);
        $this->assertEquals($accept, $attributes['accept']);
    }

    public function testHydrate()
    {
        $namespace = 'ns';
        $name = 'dp';
        $accept = 'file/*';
        $file = ComponentFactory::File($namespace ,$name, ['foo'=>'bar']);
        $this->tester->setAttributeValue($file, 'acceptType', $accept);
        $file->hydrate();
        $attributes = $this->tester->getAttributeValue($file->input, 'attributes');
        $this->assertArrayHasKey('accept', $attributes);
        $this->assertEquals($accept, $attributes['accept']);
    }
}
