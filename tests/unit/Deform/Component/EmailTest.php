<?php
namespace Deform\Component;

class EmailTest extends \Codeception\Test\Unit
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
        $email = ComponentFactory::Email($namespace ,$name, ['foo'=>'bar']);

        $autolabel = $this->tester->getAttributeValue($email, 'autoLabel');
        $this->assertTrue($autolabel);

        $container = $this->tester->getAttributeValue($email, 'componentContainer');
        $this->assertInstanceOf(\Deform\Component\ComponentContainer::class, $container);
        $control = $this->tester->getAttributeValue($container, 'control');
        $this->assertInstanceOf(\Deform\Component\ComponentControls::class, $control);

        $expectedId = 'email-'.$namespace.'-'.$name;
        $this->tester->assertIsHtmlTag($email->input,'input',[
            'id' => $expectedId,
            'name' => $namespace.'['.$name.']',
            'type' => 'email',
        ]);
    }


    public function testHydrate()
    {
        $namespace = 'ns';
        $name = 'dp';
        $email = ComponentFactory::Email($namespace, $name, ['foo' => 'bar']);
        // @todo: check hydration properly
        $this->assertNull($email->hydrate());
    }
}
