<?php
namespace App\Tests\Unit\Deform\Component;

use Deform\Component\ComponentFactory;
use Deform\Exception\DeformComponentException;

class SelectMultiTest extends \Codeception\Test\Unit
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

    public function testInvalidSetupNull()
    {
        $select = ComponentFactory::SelectMulti('ns','sl');
        $this->expectException(DeformComponentException::class);
        $this->expectExceptionMessage('Select component options must be an array');
        $select->getHtmlTag();
    }

    public function testInvalidSetup()
    {
        $select = ComponentFactory::SelectMulti('ns','sl')
            ->options([]);
        $this->expectException(DeformComponentException::class);
        $this->expectExceptionMessage('A select component must contain at least one option');
        $select->getHtmlTag();
    }

    public function testSetup()
    {
        $namespace = 'ns';
        $name = 'dt';
        $selectOptions = ['true'=>'labelTrue','false'=>'labelFalse', 'filenotfound'=>'labelFileNotFound'];
        $select = ComponentFactory::SelectMulti($namespace ,$name, ['foo'=>'bar'])
            ->options($selectOptions);

        $autolabel = $this->tester->getAttributeValue($select, 'autoLabel');
        $this->assertTrue($autolabel);

        $container = $this->tester->getAttributeValue($select, 'componentContainer');
        $this->assertInstanceOf(\Deform\Component\ComponentContainer::class, $container);
        $control = $this->tester->getAttributeValue($container, 'control');
        $this->assertInstanceOf(\Deform\Component\ComponentControls::class, $control);

        $expectedId = 'selectmulti-'.$namespace.'-'.$name;
        $this->tester->assertIsHtmlTag($select->select,'select',[
            'id' => $expectedId,
            'name' => $namespace.'['.$name.'][]',
            'multiple' => 'multiple'
        ]);
    }

    public function testSetupOptgroups()
    {
        $namespace = 'ns';
        $name = 'dt';
        $selectOptions = [
            'group1' => ['a'=>'b','c'=>'d','e'=>'f','g'=>'h'],
            'group2' => ['i'=>'j','k'=>'l']
        ];
        $select = ComponentFactory::SelectMulti($namespace ,$name, ['foo'=>'bar'])
            ->optgroupOptions($selectOptions);

        $autolabel = $this->tester->getAttributeValue($select, 'autoLabel');
        $this->assertTrue($autolabel);

        $container = $this->tester->getAttributeValue($select, 'componentContainer');
        $this->assertInstanceOf(\Deform\Component\ComponentContainer::class, $container);
        $control = $this->tester->getAttributeValue($container, 'control');
        $this->assertInstanceOf(\Deform\Component\ComponentControls::class, $control);

        $expectedId = 'selectmulti-'.$namespace.'-'.$name;
        $this->tester->assertIsHtmlTag($select->select,'select',[
            'id' => $expectedId,
            'name' => $namespace.'['.$name.'][]',
            'multiple' => 'multiple'
        ]);
    }

    public function testSetValue()
    {
        $namespace = 'ns';
        $name = 'dt';
        $selectOptions = ['true'=>'labelTrue','false'=>'labelFalse', 'filenotfound'=>'labelFileNotFound'];
        $select = ComponentFactory::SelectMulti($namespace ,$name, ['foo'=>'bar'])
            ->options($selectOptions);

        $container = $this->tester->getAttributeValue($select, 'componentContainer');
        $this->assertInstanceOf(\Deform\Component\ComponentContainer::class, $container);
        $control = $this->tester->getAttributeValue($container, 'control');
        $this->assertInstanceOf(\Deform\Component\ComponentControls::class, $control);
        $controlTags = $this->tester->getAttributeValue($control, 'controlTags');
        $this->assertCount(1, $controlTags);
        $selectTag = array_pop($controlTags);
        $this->tester->assertIsHtmlTag($selectTag, 'select', [
            'id' => 'selectmulti-'.$namespace.'-'.$name,
            'name' => $namespace.'['.$name.'][]',
            'multiple' => 'multiple'
        ]);
        $optionTags = $selectTag->getChildren();

        $selectOptionsKeys = array_keys($selectOptions);
        $this->assertSameSize($selectOptionsKeys, $optionTags);
        $optionTagsByValue = [];

        for ($idx=0; $idx<count($selectOptionsKeys); $idx++) {
            $value = $selectOptionsKeys[$idx];
            $this->tester->assertIsHtmlTag($optionTags[$idx], 'option', ['value'=>$value]);
            $optionTagsByValue[$value] = $optionTags[$idx];
        }

        // tests single
        $select->setValue('true');
        $attributes = $this->tester->getAttributeValue($optionTagsByValue['true'],'attributes');
        $this->assertArrayHasKey('selected', $attributes);
        $this->assertEquals('selected', $attributes['selected']);
        $attributes = $this->tester->getAttributeValue($optionTagsByValue['false'],'attributes');
        $this->assertArrayNotHasKey('selected', $attributes);
        $attributes = $this->tester->getAttributeValue($optionTagsByValue['filenotfound'],'attributes');
        $this->assertArrayNotHasKey('selected', $attributes);

        // test array
        $select->setValue(['true','filenotfound']);
        $attributes = $this->tester->getAttributeValue($optionTagsByValue['true'],'attributes');
        $this->assertArrayHasKey('selected', $attributes);
        $this->assertEquals('selected', $attributes['selected']);
        $attributes = $this->tester->getAttributeValue($optionTagsByValue['false'],'attributes');
        $this->assertArrayNotHasKey('selected', $attributes);
        $attributes = $this->tester->getAttributeValue($optionTagsByValue['filenotfound'],'attributes');
        $this->assertArrayHasKey('selected', $attributes);
        $this->assertEquals('selected', $attributes['selected']);
    }

    public function testSetOptgroupValue()
    {
        $namespace = 'ns';
        $name = 'dt';
        $selectOptions = [
            'group1' => ['a'=>'b','c'=>'d','e'=>'f','g'=>'h'],
            'group2' => ['i'=>'j','k'=>'l']
        ];
        $select = ComponentFactory::SelectMulti($namespace ,$name, ['foo'=>'bar'])
            ->optgroupOptions($selectOptions);

        $container = $this->tester->getAttributeValue($select, 'componentContainer');
        $this->assertInstanceOf(\Deform\Component\ComponentContainer::class, $container);
        $control = $this->tester->getAttributeValue($container, 'control');
        $this->assertInstanceOf(\Deform\Component\ComponentControls::class, $control);
        $controlTags = $this->tester->getAttributeValue($control, 'controlTags');
        $this->assertCount(1, $controlTags);
        $selectTag = array_pop($controlTags);
        $this->tester->assertIsHtmlTag($selectTag, 'select', [
            'id' => 'selectmulti-'.$namespace.'-'.$name,
            'name' => $namespace.'['.$name.'][]',
            'multiple' => 'multiple'
        ]);
        $optionGroupTags = $selectTag->getChildren();

        $selectOptionsGroupKeys = array_keys($selectOptions);
        $this->assertSameSize($selectOptionsGroupKeys, $optionGroupTags);

        $optionTagsByValue = [];
        for($groupIdx=0; $groupIdx<count($selectOptionsGroupKeys); $groupIdx++) {
            $optgroupName = $selectOptionsGroupKeys[$groupIdx];
            $options = $selectOptions[$optgroupName];
            $optionsKeys = array_keys($options);
            $optionsTags = $optionGroupTags[$groupIdx]->getChildren();
            for($idx=0; $idx<count($optionsKeys); $idx++) {
                $value = $optionsKeys[$idx];
                $optionTag = $optionsTags[$idx];
                $this->tester->assertIsHtmlTag($optionTag, 'option', ['value'=>$value]);
                $optionTagsByValue[$value] = $optionTag;
            }
        };

        // tests single
        $select->setValue('i');
        $attributes = $this->tester->getAttributeValue($optionTagsByValue['a'],'attributes');
        $this->assertArrayNotHasKey('selected', $attributes);
        $attributes = $this->tester->getAttributeValue($optionTagsByValue['c'],'attributes');
        $this->assertArrayNotHasKey('selected', $attributes);
        $attributes = $this->tester->getAttributeValue($optionTagsByValue['e'],'attributes');
        $this->assertArrayNotHasKey('selected', $attributes);
        $attributes = $this->tester->getAttributeValue($optionTagsByValue['g'],'attributes');
        $this->assertArrayNotHasKey('selected', $attributes);
        $attributes = $this->tester->getAttributeValue($optionTagsByValue['i'],'attributes');
        $this->assertArrayHasKey('selected', $attributes);
        $this->assertEquals('selected', $attributes['selected']);
        $attributes = $this->tester->getAttributeValue($optionTagsByValue['k'],'attributes');
        $this->assertArrayNotHasKey('selected', $attributes);

        // test array
        $select->setValue(['c','g','k']);
        $attributes = $this->tester->getAttributeValue($optionTagsByValue['a'],'attributes');
        $this->assertArrayNotHasKey('selected', $attributes);
        $attributes = $this->tester->getAttributeValue($optionTagsByValue['c'],'attributes');
        $this->assertArrayHasKey('selected', $attributes);
        $this->assertEquals('selected', $attributes['selected']);
        $attributes = $this->tester->getAttributeValue($optionTagsByValue['e'],'attributes');
        $this->assertArrayNotHasKey('selected', $attributes);
        $attributes = $this->tester->getAttributeValue($optionTagsByValue['g'],'attributes');
        $this->assertArrayHasKey('selected', $attributes);
        $this->assertEquals('selected', $attributes['selected']);
        $attributes = $this->tester->getAttributeValue($optionTagsByValue['i'],'attributes');
        $this->assertArrayNotHasKey('selected', $attributes);
        $attributes = $this->tester->getAttributeValue($optionTagsByValue['k'],'attributes');
        $this->assertArrayHasKey('selected', $attributes);
        $this->assertEquals('selected', $attributes['selected']);
    }
}
