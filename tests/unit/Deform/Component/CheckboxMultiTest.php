<?php
namespace Deform\Component;

use Deform\Exception\DeformComponentException;

class CheckboxMultiTest extends \Codeception\Test\Unit
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

    // tests
    public function testInvalidSetup()
    {
        $checkbox = ComponentFactory::CheckboxMulti('ns','cbm');
        $this->expectException(DeformComponentException::class);
        $this->expectExceptionMessage('Components must contain at least one control');
        $checkbox->getHtmlTag();
    }

    public function testSetup()
    {
        $namespace = 'ns';
        $name = 'cbm';
        $checkboxMultiValues = ['true'=>'labelTrue','false'=>'labelFalse', 'filenotfound'=>'labelFileNotFound'];
        $checkboxMulti = ComponentFactory::CheckboxMulti($namespace,$name, ['foo'=>'bar'])
            ->checkboxes($checkboxMultiValues);

        $autolabel = $this->tester->getAttributeValue($checkboxMulti, 'autoLabel');
        $this->assertFalse($autolabel);

        $container = $this->tester->getAttributeValue($checkboxMulti, 'componentContainer');
        $this->assertInstanceOf(\Deform\Component\ComponentContainer::class, $container);
        $control = $this->tester->getAttributeValue($container, 'control');
        $this->assertInstanceOf(\Deform\Component\ComponentControls::class, $control);

        $this->performCheckboxMultiValuesTest($namespace, $name, $checkboxMultiValues, $control);
    }

    public function testChangeCheckboxes()
    {
        $namespace = 'ns';
        $name = 'cbm';
        $checkboxMultiValues = ['true'=>'labelTrue', 'false'=>'labelFalse', 'filenotfound'=>'labelFileNotFound'];
        $checkboxMulti = ComponentFactory::CheckboxMulti($namespace,$name, ['foo'=>'bar'])
            ->checkboxes($checkboxMultiValues);
        $container = $this->tester->getAttributeValue($checkboxMulti, 'componentContainer');
        $control = $this->tester->getAttributeValue($container, 'control');
        $this->performCheckboxMultiValuesTest($namespace, $name, $checkboxMultiValues, $control);

        $newCheckboxMultiValues = ['a'=>'b', 'c'=>'d', 'e'=>'f', 'g'=>'h'];
        $checkboxMulti->checkboxes($newCheckboxMultiValues);
        $this->performCheckboxMultiValuesTest($namespace, $name, $newCheckboxMultiValues, $control);
    }

    private function performCheckboxMultiValuesTest($namespace, $name, $checkboxMultiValues, $control)
    {
        $checkboxMultiValuesCount = count($checkboxMultiValues);
        $checkboxMultiValueKeys = array_keys($checkboxMultiValues);
        $controlTags = $this->tester->getAttributeValue($control, 'controlTags');
        $allTags = $this->tester->getAttributeValue($control, 'allTags');
        $this->assertEquals($checkboxMultiValuesCount, count($controlTags));
        $this->assertEquals($checkboxMultiValuesCount, count($allTags));
        for($idx=0; $idx<$checkboxMultiValuesCount; $idx++)   {
            $checkValue = $checkboxMultiValueKeys[$idx];
            $checkLabel = $checkboxMultiValues[$checkValue];
            $expectedId = 'checkboxmulti-'.$namespace.'-'.$name.'-'.$checkValue;
            $inputAttributes = [
                'type' => 'checkbox',
                'id' => $expectedId,
                'name' => $namespace.'['.$name.'][]',
                'value' => $checkValue,
            ];
            $this->tester->assertIsHtmlTag($controlTags[$idx], 'input', $inputAttributes);
            $controlTagWrapper = $allTags[$idx];
            $this->tester->assertIsHtmlTag($controlTagWrapper,'div',['class'=>'checkboxmulti-checkbox-wrapper']);
            $controlTagChildren = $controlTagWrapper->getChildren();
            $this->assertCount(3, $controlTagChildren);
            list($input, $space, $label) = $controlTagChildren;
            $this->tester->assertIsHtmlTag($input,'input',$inputAttributes);
            $this->assertEquals(' ',$space);
            $this->tester->assertIsHtmlTag($label, 'label', ['for'=>$expectedId]);
            $labelChildren = $label->getChildren();
            $this->assertCount(1, $labelChildren);
            $this->assertEquals($checkLabel, $labelChildren[0]);
        }
    }

    public function testHydrate()
    {
        $namespace = 'ns';
        $name = 'cb';
        $checkboxMulti = ComponentFactory::CheckboxMulti($namespace, $name, ['foo' => 'bar']);
        $checkboxMultiValues = ['a'=>'b','c'=>'d','e'=>'f','g'=>'h'];
        $this->tester->setAttributeValue($checkboxMulti, 'checkboxValues', $checkboxMultiValues);
        $checkboxMulti->hydrate();
        $container = $this->tester->getAttributeValue($checkboxMulti, 'componentContainer');
        $control = $this->tester->getAttributeValue($container, 'control');
        $this->performCheckboxMultiValuesTest($namespace, $name, $checkboxMultiValues, $control);
    }

    public function testSetValue()
    {
        $namespace = 'ns';
        $name = 'cb';
        $checkboxMultiValues = ['a'=>'b','c'=>'d','e'=>'f','g'=>'h','i'=>'j'];
        $checkboxMulti = ComponentFactory::CheckboxMulti($namespace, $name, ['foo' => 'bar'])
            ->checkboxes($checkboxMultiValues);

        $setValue = ['a','c','i'];
        $checkboxMulti->setValue($setValue);

        $container = $this->tester->getAttributeValue($checkboxMulti, 'componentContainer');
        $control = $this->tester->getAttributeValue($container, 'control');
        $controlTags = $this->tester->getAttributeValue($control, 'controlTags');
        foreach ($controlTags as $controlTag) {
            $attributes = $this->tester->getAttributeValue($controlTag, 'attributes');
            if (in_array($attributes['value'], $setValue)) {
                $this->assertArrayHasKey('checked', $attributes);
                $this->assertEquals('checked', $attributes['checked']);
            }
            else {
                $this->assertArrayNotHasKey('checked', $attributes);
            }
        }
    }
}