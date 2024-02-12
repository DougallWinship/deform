<?php
namespace Deform\Component;

class TextAreaTest extends \Codeception\Test\Unit
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
        $name = 'ta';
        $textarea = ComponentFactory::TextArea($namespace ,$name, ['foo'=>'bar']);

        $autolabel = $this->tester->getAttributeValue($textarea, 'autoLabel');
        $this->assertTrue($autolabel);

        $container = $this->tester->getAttributeValue($textarea, 'componentContainer');
        $this->assertInstanceOf(\Deform\Component\ComponentContainer::class, $container);
        $control = $this->tester->getAttributeValue($container, 'control');
        $this->assertInstanceOf(\Deform\Component\ComponentControls::class, $control);

        $expectedId = 'textarea-'.$namespace.'-'.$name;
        $this->tester->assertIsHtmlTag($textarea->textarea,'textarea',[
            'id' => $expectedId,
            'name' => $namespace.'['.$name.']',
        ]);
    }

    public function testSetValue()
    {
        $namespace = 'ns';
        $name = 'ta';
        $textarea = ComponentFactory::TextArea($namespace ,$name, ['foo'=>'bar']);

        $value = "this is some junk";
        $textarea->setValue($value);

        $textareaTag = $textarea->textarea;
        $textareaTagChildren = $textareaTag->getChildren();

        $this->assertCount(1, $textareaTagChildren);
        $this->assertEquals($value, $textareaTagChildren[0]);
    }

    public function testHydrate()
    {
        $namespace = 'ns';
        $name = 'ta';
        $textarea = ComponentFactory::TextArea($namespace ,$name, ['foo'=>'bar']);
        // @todo: check hydration properly
        $this->assertNull($textarea->hydrate());

    }
}
