<?php
namespace App\Tests\Unit\Deform\Component;

use Deform\Component\ComponentFactory;

class CurrencyTest extends \Codeception\Test\Unit
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
        $name = 'cr';
        $currency = ComponentFactory::Currency($namespace ,$name, ['foo'=>'bar']);

        $autolabel = $this->tester->getAttributeValue($currency, 'autoLabel');
        $this->assertTrue($autolabel);

        $container = $this->tester->getAttributeValue($currency, 'componentContainer');
        $this->assertInstanceOf(\Deform\Component\ComponentContainer::class, $container);
        $control = $this->tester->getAttributeValue($container, 'control');
        $this->assertInstanceOf(\Deform\Component\ComponentControls::class, $control);

        $expectedId = 'currency-'.$namespace.'-'.$name;
        $this->tester->assertIsHtmlTag($currency->input,'input',[
            'id' => $expectedId,
            'name' => $namespace.'['.$name.']',
            'type' => 'text',
        ]);
    }

    public function testChangeCurrency()
    {
        $currency = ComponentFactory::Currency('ns', 'cr', ['foo'=>'bar']);
        $newCurrencySymbol = '$';
        $currency->currency($newCurrencySymbol);
        $container = $this->tester->getAttributeValue($currency, 'componentContainer');
        $control = $this->tester->getAttributeValue($container, 'control');
        $allControls = $this->tester->getAttributeValue($control, 'allTags');
        $this->assertCount(3, $allControls);
        list($currencyLabel, $space, $input) = $allControls;
        $this->tester->assertIsHtmlTag($currencyLabel,'label',['class'=>'currency-symbol']);
        $currencyLabelChildren = $currencyLabel->getChildren();
        $this->assertCount(1, $currencyLabelChildren);
        $this->assertEquals($newCurrencySymbol, $currencyLabelChildren[0]);
    }

    public function testHydrate()
    {
        $currency = ComponentFactory::Currency('ns', 'cr', ['foo'=>'bar']);
        $useCurrencySymbol = '$';
        $this->tester->setAttributeValue($currency,'currencyLabelValue', $useCurrencySymbol);
        $currency->hydrate();
        $container = $this->tester->getAttributeValue($currency, 'componentContainer');
        $control = $this->tester->getAttributeValue($container, 'control');
        $allTags = $this->tester->getAttributeValue($control, 'allTags');
        $this->assertCount(3, $allTags);
        list($currencyLabel, $space, $input) = $allTags;
        $this->tester->assertIsHtmlTag($currencyLabel,'label',['class'=>'currency-symbol']);
        $currencyLabelChildren = $currencyLabel->getChildren();
        $this->assertCount(1, $currencyLabelChildren);
        $this->assertEquals($useCurrencySymbol, $currencyLabelChildren[0]);
    }
}
