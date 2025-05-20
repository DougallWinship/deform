<?php
namespace App\Tests\Unit\Deform\Component;

use Deform\Component\ComponentFactory;

class PasswordTest extends \Codeception\Test\Unit
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
        $password = ComponentFactory::Password($namespace ,$name, ['foo'=>'bar']);

        $autolabel = $this->tester->getAttributeValue($password, 'autoLabel');
        $this->assertTrue($autolabel);

        $container = $this->tester->getAttributeValue($password, 'componentContainer');
        $this->assertInstanceOf(\Deform\Component\ComponentContainer::class, $container);
        $control = $this->tester->getAttributeValue($container, 'control');
        $this->assertInstanceOf(\Deform\Component\ComponentControls::class, $control);

        $expectedId = 'password-'.$namespace.'-'.$name;
        $this->tester->assertIsHtmlTag($password->input,'input',[
            'id' => $expectedId,
            'name' => $namespace.'['.$name.']',
            'type' => 'password',
        ]);
    }
}
