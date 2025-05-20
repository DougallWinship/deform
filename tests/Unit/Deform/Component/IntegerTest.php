<?php
namespace App\Tests\Unit\Deform\Component;

use Deform\Component\ComponentFactory;
use Deform\Exception\DeformComponentException;

class IntegerTest extends \Codeception\Test\Unit
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
        $name = 'in';
        $integer = ComponentFactory::Integer($namespace ,$name, ['foo'=>'bar']);

        $autolabel = $this->tester->getAttributeValue($integer, 'autoLabel');
        $this->assertTrue($autolabel);

        $container = $this->tester->getAttributeValue($integer, 'componentContainer');
        $this->assertInstanceOf(\Deform\Component\ComponentContainer::class, $container);
        $control = $this->tester->getAttributeValue($container, 'control');
        $this->assertInstanceOf(\Deform\Component\ComponentControls::class, $control);

        $expectedId = 'integer-'.$namespace.'-'.$name;
        $this->tester->assertIsHtmlTag($integer->input,'input',[
            'id' => $expectedId,
            'name' => $namespace.'['.$name.']',
            'type' => 'number',
        ]);
    }

    public function testMin()
    {
        $integer = ComponentFactory::Integer(null, 'in');
        $min = 10;
        $integer->min($min);

        $checkMax = $this->tester->getAttributeValue($integer, 'min');
        $this->assertEquals($min, $checkMax);

        $this->tester->assertIsHtmlTag($integer->input, 'input', [
            'min' => $min,
        ]);
    }

    public function testNonNumericMin()
    {
        $integer = ComponentFactory::Integer(null, 'in');
        $this->expectException(DeformComponentException::class);
        $integer->min('apples');
    }

    public function testMinMoreThanMax()
    {
        $integer = ComponentFactory::Integer(null, 'in');
        $integer->max(0);
        $this->expectException(DeformComponentException::class);
        $integer->min(1);
    }

    public function testMax()
    {
        $integer = ComponentFactory::Integer(null, 'in');
        $max = 10;
        $integer->max($max);

        $checkMax = $this->tester->getAttributeValue($integer, 'max');
        $this->assertEquals($max, $checkMax);

        $this->tester->assertIsHtmlTag($integer->input, 'input', [
            'max' => $max,
        ]);
    }

    public function testNonNumericMax()
    {
        $integer = ComponentFactory::Integer(null, 'in');
        $this->expectException(DeformComponentException::class);
        $integer->max('oranges');
    }

    public function testMaxLessThanMin()
    {
        $integer = ComponentFactory::Integer(null, 'in');
        $integer->min(1);
        $this->expectException(DeformComponentException::class);
        $integer->max(0);
    }

    public function testStep()
    {
        $integer = ComponentFactory::Integer(null, 'in');
        $step = 5;
        $integer->step($step);

        $checkStep = $this->tester->getAttributeValue($integer, 'step');
        $this->assertEquals($step, $checkStep);

        $this->tester->assertIsHtmlTag($integer->input, 'input', [
            'step' => $step,
        ]);
    }

    public function testNonNumericStep()
    {
        $integer = ComponentFactory::Integer(null, 'in');
        $this->expectException(DeformComponentException::class);
        $integer->step("wibble");
    }

    public function testStepLessThanOne()
    {
        $integer = ComponentFactory::Integer(null, 'in');
        $this->expectException(DeformComponentException::class);
        $integer->step(-5);
    }

    public function testStepZero()
    {
        $integer = ComponentFactory::Integer(null, 'in');
        $this->expectException(DeformComponentException::class);
        $integer->step(0);
    }

    public function testHydrate()
    {
        $decimal = ComponentFactory::Integer(null, 'in');
        $min = -20;
        $max = 20;
        $step = 5;
        $this->tester->setAttributeValue($decimal, 'min', $min);
        $this->tester->setAttributeValue($decimal, 'max', $max);
        $this->tester->setAttributeValue($decimal, 'step', $step);
        $decimal->hydrate();
        $this->tester->assertIsHtmlTag($decimal->input, 'input', [
            'min' => $min,
            'max' => $max,
            'step' => $step,
        ]);
    }
}
