<?php
namespace App\Tests\Unit\Deform\Component;

use Deform\Component\ComponentFactory;

class SliderTest extends \Codeception\Test\Unit
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
        $display = ComponentFactory::Slider($namespace ,$name, ['foo'=>'bar']);

        $autolabel = $this->tester->getAttributeValue($display, 'autoLabel');
        $this->assertTrue($autolabel);

        $container = $this->tester->getAttributeValue($display, 'componentContainer');
        $this->assertInstanceOf(\Deform\Component\ComponentContainer::class, $container);
        $control = $this->tester->getAttributeValue($container, 'control');
        $this->assertInstanceOf(\Deform\Component\ComponentControls::class, $control);

        $expectedId = 'slider-'.$namespace.'-'.$name;
        $this->tester->assertIsHtmlTag($display->input,'input',[
            'id' => $expectedId,
            'name' => $namespace.'['.$name.']',
            'type' => 'range',
        ]);
    }

    public function testShowOutput()
    {
        $namespace = 'ns';
        $name = 'dp';
        $display = ComponentFactory::Slider($namespace ,$name, ['foo'=>'bar'])->showOutput();

        $container = $this->tester->getAttributeValue($display, 'componentContainer');
        $this->assertInstanceOf(\Deform\Component\ComponentContainer::class, $container);
        $control = $this->tester->getAttributeValue($container, 'control');
        $this->assertInstanceOf(\Deform\Component\ComponentControls::class, $control);

        $controlTags = $this->tester->getAttributeValue($control, 'allTags');
        $this->assertCount(2, $controlTags);

        $inputTag = $controlTags[0];
        $this->assertInstanceOf(\Deform\Html\HtmlTag::class, $inputTag);
        $expectedId = 'slider-'.$namespace.'-'.$name;
        $this->tester->assertIsHtmlTag($inputTag,'input',[
            'id' => $expectedId,
            'name' => $namespace.'['.$name.']',
            'type' => 'range',
            'oninput' => 'this.nextElementSibling.value=this.value',
        ]);

        $outputTag = $controlTags[1];
        $this->tester->assertIsHtmlTag($outputTag, 'output', [
            'class'=>'slider-output'
        ]);
    }
}
