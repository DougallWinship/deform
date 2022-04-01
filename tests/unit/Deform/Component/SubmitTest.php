<?php
namespace Deform\Component;

class SubmitTest extends \Codeception\Test\Unit
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
        $submit = ComponentFactory::Submit($namespace ,$name, ['foo'=>'bar']);

        $autolabel = $this->tester->getAttributeValue($submit, 'autoLabel');
        $this->assertFalse($autolabel);

        $container = $this->tester->getAttributeValue($submit, 'componentContainer');
        $this->assertInstanceOf(\Deform\Component\ComponentContainer::class, $container);
        $control = $this->tester->getAttributeValue($container, 'control');
        $this->assertInstanceOf(\Deform\Component\ComponentControls::class, $control);
        $controlOnly = $this->tester->getAttributeValue($container, 'controlOnly');
        $this->assertTrue($controlOnly);

        $expectedId = 'submit-'.$namespace.'-'.$name;
        $this->tester->assertIsHtmlTag($submit->input,'input',[
            'id' => $expectedId,
            'name' => $namespace.'['.$name.']',
            'type' => 'submit',
        ]);
    }

    public function testHydrate() {
        $namespace = 'ns';
        $name = 'dp';
        $submit = ComponentFactory::Submit($namespace ,$name, ['foo'=>'bar']);
        $this->assertNull($submit->hydrate());
    }
}
