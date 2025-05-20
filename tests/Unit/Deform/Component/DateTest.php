<?php
namespace App\Tests\Unit\Deform\Component;

use Deform\Component\ComponentFactory;

class DateTest extends \Codeception\Test\Unit
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
        $date = ComponentFactory::Date($namespace ,$name, ['foo'=>'bar']);

        $autolabel = $this->tester->getAttributeValue($date, 'autoLabel');
        $this->assertTrue($autolabel);

        $container = $this->tester->getAttributeValue($date, 'componentContainer');
        $this->assertInstanceOf(\Deform\Component\ComponentContainer::class, $container);
        $control = $this->tester->getAttributeValue($container, 'control');
        $this->assertInstanceOf(\Deform\Component\ComponentControls::class, $control);


        $expectedId = 'date-'.$namespace.'-'.$name;
        $this->tester->assertIsHtmlTag($date->input,'input',[
            'id' => $expectedId,
            'name' => $namespace.'['.$name.']',
            'type' => 'date',
        ]);
    }

    public function testHydrate()
    {
        $namespace = 'ns';
        $name = 'dt';
        $date = ComponentFactory::Date($namespace ,$name, ['foo'=>'bar']);
        $date->hydrate();
        $container = $this->tester->getAttributeValue($date, 'componentContainer');
        $control = $this->tester->getAttributeValue($container, 'control');
        $allTags = $this->tester->getAttributeValue($control, 'allTags');
        $this->assertCount(1, $allTags);
        $inputDate = $allTags[0];
        $this->tester->assertIsHtmlTag($inputDate, "input", ["type"=>"date"]);
    }
}
