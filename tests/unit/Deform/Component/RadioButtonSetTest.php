<?php
namespace Deform\Component;

use Deform\Util\Arrays;

class RadioButtonSetTest extends \Codeception\Test\Unit
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

    public function testInvalidSetup()
    {
        $checkbox = ComponentFactory::CheckboxMulti('ns','rbs');
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Components must contain at least one control');
        $checkbox->getHtmlTag();
    }

    public function testSetupNonAssoc()
    {
        $namespace = 'ns';
        $name = 'dt';
        $radioButtonSetValues = ['true','false', 'filenotfound'];
        $radioButtonSet = ComponentFactory::RadioButtonSet($namespace ,$name, ['foo'=>'bar'])
            ->radioButtons($radioButtonSetValues);

        $autolabel = $this->tester->getAttributeValue($radioButtonSet, 'autoLabel');
        $this->assertTrue($autolabel);

        $container = $this->tester->getAttributeValue($radioButtonSet, 'componentContainer');
        $this->assertInstanceOf(\Deform\Component\ComponentContainer::class, $container);
        $control = $this->tester->getAttributeValue($container, 'control');
        $this->assertInstanceOf(\Deform\Component\ComponentControls::class, $control);

        $this->performRadioButtonSetValuesTest($namespace, $name, $radioButtonSetValues, $control);

    }

    public function testSetupAssoc()
    {
        $namespace = 'ns';
        $name = 'dt';
        $radioButtonSetValues = ['true'=>'labelTrue','false'=>'labelFalse', 'filenotfound'=>'labelFileNotFound'];
        $radioButtonSet = ComponentFactory::RadioButtonSet($namespace ,$name, ['foo'=>'bar'])
            ->radioButtons($radioButtonSetValues);

        $autolabel = $this->tester->getAttributeValue($radioButtonSet, 'autoLabel');
        $this->assertTrue($autolabel);

        $container = $this->tester->getAttributeValue($radioButtonSet, 'componentContainer');
        $this->assertInstanceOf(\Deform\Component\ComponentContainer::class, $container);
        $control = $this->tester->getAttributeValue($container, 'control');
        $this->assertInstanceOf(\Deform\Component\ComponentControls::class, $control);

        $this->performRadioButtonSetValuesTest($namespace, $name, $radioButtonSetValues, $control);
    }

    private function performRadioButtonSetValuesTest($namespace, $name, $radioButtonSetValues, $control)
    {
        $isAssoc = Arrays::isAssoc($radioButtonSetValues);
        $radioButtonSetValuesCount = count($radioButtonSetValues);
        $radioButtonSetValueKeys = array_keys($radioButtonSetValues);
        $controlTags = $this->tester->getAttributeValue($control, 'controlTags');
        $allTags = $this->tester->getAttributeValue($control, 'allTags');
        $this->assertEquals($radioButtonSetValuesCount, count($controlTags));
        $this->assertEquals($radioButtonSetValuesCount, count($allTags));
        for($idx=0; $idx<$radioButtonSetValuesCount; $idx++)   {
            if ($isAssoc) {
                $checkValue = $radioButtonSetValueKeys[$idx];
                $checkLabel = $radioButtonSetValues[$checkValue];
            }
            else {
                $checkValue = $radioButtonSetValues[$idx];
                $checkLabel = $radioButtonSetValues[$idx];
            }
            $expectedId = 'radiobuttonset-'.$namespace.'-'.$name.'-'.$checkValue;
            $inputAttributes = [
                'type' => 'radio',
                'id' => $expectedId,
                'name' => $namespace.'['.$name.']',
                'value' => $checkValue,
            ];
            $this->tester->assertIsHtmlTag($controlTags[$idx], 'input', $inputAttributes);
            $controlTagWrapper = $allTags[$idx];
            $this->tester->assertIsHtmlTag($controlTagWrapper,'div',['class'=>'radiobuttonset-radio-container']);
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

    public function testAlterSelected()
    {
        $namespace = 'ns';
        $name = 'dt';
        $radioButtonSetValues = ['true'=>'labelTrue','false'=>'labelFalse', 'filenotfound'=>'labelFileNotFound'];
        $setRadioButtonValue = 'true';
        $radioButtonSet = ComponentFactory::RadioButtonSet($namespace ,$name, ['foo'=>'bar'])
            ->radioButtons($radioButtonSetValues);
        $radioButtonSet->setValue($setRadioButtonValue);

        $container = $this->tester->getAttributeValue($radioButtonSet, 'componentContainer');
        $this->assertInstanceOf(\Deform\Component\ComponentContainer::class, $container);
        $control = $this->tester->getAttributeValue($container, 'control');
        $this->assertInstanceOf(\Deform\Component\ComponentControls::class, $control);

        $controlTags = $this->tester->getAttributeValue($control, 'controlTags');
        foreach ($controlTags as $controlTag) {
            $attributes = $this->tester->getAttributeValue($controlTag, 'attributes');
            if ($attributes['value'] === $setRadioButtonValue) {
                $this->assertArrayHasKey('checked', $attributes);
                $this->assertTrue($attributes['checked']);
            }
            else
            {
                $this->assertArrayNotHasKey('checked', $attributes);
            }
        }

        $radioButtonSet->clearSelected();
        foreach ($controlTags as $controlTag) {
            $attributes = $this->tester->getAttributeValue($controlTag, 'attributes');
            $this->assertArrayNotHasKey('checked', $attributes);
        }

        $setRadioButtonValue = 'filenotfound';
        $radioButtonSet->setValue($setRadioButtonValue);
        foreach ($controlTags as $controlTag) {
            $attributes = $this->tester->getAttributeValue($controlTag, 'attributes');
            if ($attributes['value'] === $setRadioButtonValue) {
                $this->assertArrayHasKey('checked', $attributes);
                $this->assertTrue($attributes['checked']);
            }
            else
            {
                $this->assertArrayNotHasKey('checked', $attributes);
            }
        }
    }

    public function testHydrate()
    {
        $namespace = 'ns';
        $name = 'dt';
        $radioButtonSetValues = ['true'=>'labelTrue','false'=>'labelFalse', 'filenotfound'=>'labelFileNotFound'];
        $radioButtonSet = ComponentFactory::RadioButtonSet($namespace ,$name, ['foo'=>'bar']);
        $this->tester->setAttributeValue($radioButtonSet, 'radioButtons', $radioButtonSetValues);

        $radioButtonSet->hydrate();

        $container = $this->tester->getAttributeValue($radioButtonSet, 'componentContainer');
        $this->assertInstanceOf(\Deform\Component\ComponentContainer::class, $container);
        $control = $this->tester->getAttributeValue($container, 'control');
        $this->assertInstanceOf(\Deform\Component\ComponentControls::class, $control);

        $controlTags = $this->tester->getAttributeValue($control, 'controlTags');
        $this->assertSameSize($controlTags, $radioButtonSetValues);

        $values = array_keys($radioButtonSetValues);
        for ($idx=0; $idx<count($values); $idx++) {
            $attributes = $this->tester->getAttributeValue($controlTags[$idx], 'attributes');
            $this->assertArrayHasKey('value', $attributes);
            $this->assertEquals($values[$idx], $attributes['value']);
        }
    }

    public function testShadowJavascript()
    {
        $namespace = 'ns';
        $name = 'dt';
        $radioButtonSetValues = ['true'=>'labelTrue','false'=>'labelFalse', 'filenotfound'=>'labelFileNotFound'];
        $radioButtonSet = ComponentFactory::RadioButtonSet($namespace ,$name, ['foo'=>'bar'])
            ->radioButtons($radioButtonSetValues);
        $shadowJavascript = $radioButtonSet->getShadowJavascript();
        $this->assertArrayHasKey('.control-container .radiobuttonset-radio-container', $shadowJavascript);
        $this->assertArrayHasKey('.component-container input[type=hidden]', $shadowJavascript);
        $this->assertArrayHasKey('.hint-container', $shadowJavascript);
        $this->assertArrayHasKey('.error-container', $shadowJavascript);
    }
}
