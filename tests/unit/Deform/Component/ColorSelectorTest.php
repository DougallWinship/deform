<?php
namespace Deform\Component;

class ColorSelectorTest extends \Codeception\Test\Unit
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
        $name = 'cs';
        $colorSelector = ComponentFactory::ColorSelector($namespace ,$name, ['foo'=>'bar']);

        $autolabel = $this->tester->getAttributeValue($colorSelector, 'autoLabel');
        $this->assertTrue($autolabel);

        $container = $this->tester->getAttributeValue($colorSelector, 'componentContainer');
        $this->assertInstanceOf(\Deform\Component\ComponentContainer::class, $container);
        $control = $this->tester->getAttributeValue($container, 'control');
        $this->assertInstanceOf(\Deform\Component\ComponentControls::class, $control);

        $expectedId = 'colorselector-'.$namespace.'-'.$name;
        $this->tester->assertIsHtmlTag($colorSelector->input,'input',[
            'id' => $expectedId,
            'name' => $namespace.'['.$name.']',
            'type' => 'color'
        ]);
    }

    public function testHydrate()
    {
        $namespace = 'ns';
        $name = 'dt';
        $colorSelector = ComponentFactory::ColorSelector($namespace ,$name, ['foo'=>'bar']);
        $colorSelector->hydrate();
        $container = $this->tester->getAttributeValue($colorSelector, 'componentContainer');
        $control = $this->tester->getAttributeValue($container, 'control');
        $allTags = $this->tester->getAttributeValue($control, 'allTags');
        $this->assertCount(1, $allTags);
        $inputDate = $allTags[0];
        $this->tester->assertIsHtmlTag($inputDate, "input", ["type"=>"color"]);
    }

    public function testShadowJavascript()
    {
        $currency = ComponentFactory::ColorSelector('ns', 'cr', ['foo'=>'bar']);
        $shadowJs = $currency->shadowJavascript();
        $this->assertCount(1, $shadowJs);
        $this->assertArrayHasKey('.control-container input', $shadowJs);
    }

}
