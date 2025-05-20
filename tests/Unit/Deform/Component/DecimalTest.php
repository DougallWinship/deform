<?php
namespace App\Tests\Unit\Deform\Component;

use Deform\Component\ComponentFactory;
use Deform\Component\Decimal;
use Deform\Exception\DeformComponentException;
use Deform\Util\Strings;

class DecimalTest extends \Codeception\Test\Unit
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
        $name = 'de';
        $decimal = ComponentFactory::Decimal($namespace, $name, ['foo' => 'bar']);

        $autolabel = $this->tester->getAttributeValue($decimal, 'autoLabel');
        $this->assertTrue($autolabel);

        $container = $this->tester->getAttributeValue($decimal, 'componentContainer');
        $this->assertInstanceOf(\Deform\Component\ComponentContainer::class, $container);
        $control = $this->tester->getAttributeValue($container, 'control');
        $this->assertInstanceOf(\Deform\Component\ComponentControls::class, $control);

        $expectedId = 'decimal-' . $namespace . '-' . $name;
        $this->tester->assertIsHtmlTag($decimal->input, 'input', [
            'id' => $expectedId,
            'name' => $namespace . '[' . $name . ']',
            'type' => 'text',
        ]);
    }

    public function testMin()
    {
        $decimal = ComponentFactory::Decimal(null, 'de');
        $min = -10;
        $decimal->min($min);

        $checkMin = $this->tester->getAttributeValue($decimal, 'min');
        $this->assertEquals($min, $checkMin);

        $this->tester->assertIsHtmlTag($decimal->input, 'input', [
            'data-min' => $min,
        ]);
    }

    public function testNonNumericMin()
    {
        $decimal = ComponentFactory::Decimal(null, 'de');
        $this->expectException(DeformComponentException::class);
        $decimal->min("apples");
    }

    public function testMinMoreThanMax()
    {
        $decimal = ComponentFactory::Decimal(null, 'de');
        $decimal->max(0);
        $this->expectException(DeformComponentException::class);
        $decimal->min(1);
    }

    public function testMax()
    {
        $decimal = ComponentFactory::Decimal(null, 'de');
        $max = 10;
        $decimal->max($max);

        $checkMin = $this->tester->getAttributeValue($decimal, 'max');
        $this->assertEquals($max, $checkMin);

        $this->tester->assertIsHtmlTag($decimal->input, 'input', [
            'data-max' => $max,
        ]);
    }

    public function testNonNumericMax()
    {
        $decimal = ComponentFactory::Decimal(null, 'de');
        $this->expectException(DeformComponentException::class);
        $decimal->max("oranges");
    }

    public function testMaxLessThanMin()
    {
        $decimal = ComponentFactory::Decimal(null, 'de');
        $decimal->min(0);
        $this->expectException(DeformComponentException::class);
        $decimal->max(-1);
    }

    public function testDecimalPlaces()
    {
        $decimal = ComponentFactory::Decimal(null, 'de');
        $dp = 5;
        $decimal->dp($dp);

        $checkMin = $this->tester->getAttributeValue($decimal, 'dp');
        $this->assertEquals($dp, $checkMin);

        $this->tester->assertIsHtmlTag($decimal->input, 'input', [
            'data-dp' => $dp,
        ]);
    }

    public function testInvalidDecimalPlaces()
    {
        $decimal = ComponentFactory::Decimal(null, 'de');
        $this->expectException(DeformComponentException::class);
        $decimal->dp(-1);
    }

    public function testRoundingStrategy()
    {
        $decimal = ComponentFactory::Decimal(null, 'de');
        $strategy = Decimal::ROUND_BANKERS;
        $decimal->roundStrategy($strategy);
        $checkStrategy = $this->tester->getAttributeValue($decimal,'strategy');
        $this->assertEquals($strategy, $checkStrategy);

        $this->tester->assertIsHtmlTag($decimal->input,'input',[
            'data-round' => $strategy,
        ]);
    }

    public function testInvalidRoundingStrategy()
    {
        $decimal = ComponentFactory::Decimal(null, 'de');
        $this->expectException(DeformComponentException::class);
        $decimal->roundStrategy("not a valid strategy");
    }

    public function testHydrate()
    {
        $decimal = ComponentFactory::Decimal(null, 'de');
        $min = -10;
        $max = 10;
        $dp = 3;
        $strategy = Decimal::ROUND_FLOOR;
        $this->tester->setAttributeValue($decimal, 'min', $min);
        $this->tester->setAttributeValue($decimal, 'max', $max);
        $this->tester->setAttributeValue($decimal, 'dp', $dp);
        $this->tester->setAttributeValue($decimal, 'strategy', $strategy);
        $decimal->hydrate();
        $this->tester->assertIsHtmlTag($decimal->input, 'input', [
            'data-min' => $min,
            'data-max' => $max,
            'data-dp' => $dp,
            'data-round' => $strategy,
        ]);
    }
}
