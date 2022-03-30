<?php
namespace Deform\Component;

class SelectTest extends \Codeception\Test\Unit
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
        $select = ComponentFactory::Select('ns','sl');
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('A select component must contain at least one option');
        $select->getHtmlTag();
    }

    public function testValidSetup()
    {
        $selectOptions = ['true'=>'labelTrue','false'=>'labelFalse', 'filenotfound'=>'labelFileNotFound'];
        $select = ComponentFactory::Select('ns','sl')
            ->options($selectOptions);
        $this->assertFalse($select->hasOptGroups);
        $selectComponentTag = $select->getHtmlTag();
        $this->tester->assertIsHtmlTag($selectComponentTag, 'div', ['id' => 'ns-sl-container']);
    }

    public function testValidOptgroupSetup()
    {
        $selectOptions = [
            'group1' => ['a'=>'b','c'=>'d','e'=>'f','g'=>'h'],
            'group2' => ['i'=>'j','k'=>'l']
        ];
        $select = ComponentFactory::Select('ns','sl')
            ->optgroupOptions($selectOptions);
        $this->assertTrue($select->hasOptGroups);
        $selectComponentTag = $select->getHtmlTag();
        $this->tester->assertIsHtmlTag($selectComponentTag, 'div', ['id' => 'ns-sl-container']);
    }

    public function testSetup()
    {
        $namespace = 'ns';
        $name = 'dt';
        $selectOptions = ['true'=>'labelTrue','false'=>'labelFalse', 'filenotfound'=>'labelFileNotFound'];
        $select = ComponentFactory::Select($namespace ,$name, ['foo'=>'bar'])
            ->options($selectOptions);

        $autolabel = $this->tester->getAttributeValue($select, 'autoLabel');
        $this->assertTrue($autolabel);

        $container = $this->tester->getAttributeValue($select, 'componentContainer');
        $this->assertInstanceOf(\Deform\Component\ComponentContainer::class, $container);
        $control = $this->tester->getAttributeValue($container, 'control');
        $this->assertInstanceOf(\Deform\Component\ComponentControls::class, $control);

        $expectedId = 'select-'.$namespace.'-'.$name;
        $this->tester->assertIsHtmlTag($select->select,'select',[
            'id' => $expectedId,
            'name' => $namespace.'['.$name.']',
        ]);
    }

    public function testOptions()
    {
        $namespace = 'ns';
        $name = 'dt';
        $selectOptions = ['true'=>'labelTrue','false'=>'labelFalse', 'filenotfound'=>'labelFileNotFound'];
        $select = ComponentFactory::Select($namespace ,$name, ['foo'=>'bar'])
            ->options($selectOptions);

        $newSelectOptions = ['a'=>'b','c'=>'d','e'=>'f','g'=>'h'];
        $select->options($newSelectOptions);
    }

    public function testOptgroupOptions()
    {
        $namespace = 'ns';
        $name = 'dt';
        $selectOptions = ['true'=>'labelTrue','false'=>'labelFalse', 'filenotfound'=>'labelFileNotFound'];
        $select = ComponentFactory::Select($namespace ,$name, ['foo'=>'bar'])
            ->options($selectOptions);

        $newSelectOptions = [
            'group1' => ['a'=>'b','c'=>'d','e'=>'f','g'=>'h'],
            'group2' => ['i'=>'j','k'=>'l']
        ];
        $select->optgroupOptions($newSelectOptions);

        $container = $this->tester->getAttributeValue($select, 'componentContainer');
        $this->assertInstanceOf(\Deform\Component\ComponentContainer::class, $container);
        $control = $this->tester->getAttributeValue($container, 'control');
        $this->assertInstanceOf(\Deform\Component\ComponentControls::class, $control);

        $inputControls = $this->tester->getAttributeValue($control,'controlTags');
        $this->assertCount(1, $inputControls);
        $selectTag = $inputControls[0];
        $selectGroups = $selectTag->getChildren();

        $this->assertCount(2, $selectGroups);
        $newGroups = array_keys($newSelectOptions);
        $optgroup1Tag = $selectGroups[0];
        $this->tester->assertIsHtmlTag($optgroup1Tag,'optgroup', ['label'=>$newGroups[0]]);
        $newGroup1Values = $newSelectOptions[$newGroups[0]];
        $newGroup1Keys = array_keys($newGroup1Values);
        $optgroup1OptionTags = $optgroup1Tag->getChildren();
        $this->assertSameSize($newGroup1Values, $optgroup1OptionTags);
        for ($idx=0; $idx<count($newGroup1Keys); $idx++) {
            $optionValue = $newGroup1Keys[$idx];
            $optionText = $newGroup1Values[$optionValue];
            $option = $optgroup1OptionTags[$idx];
            $this->tester->assertIsHtmlTag($option,'option',['value' => $optionValue]);
            $values = $option->getChildren();
            $this->assertCount(1, $values);
            $this->assertEquals($optionText, $values[0]);
        }

        $optgroup2Tag = $selectGroups[1];
        $this->tester->assertIsHtmlTag($optgroup2Tag,'optgroup', ['label'=>$newGroups[1]]);
        $newGroup2Values = $newSelectOptions[$newGroups[1]];
        $newGroup2Keys = array_keys($newSelectOptions[$newGroups[1]]);
        $optgroup2OptionTags = $optgroup2Tag->getChildren();
        $this->assertSameSize($newGroup2Values, $optgroup2OptionTags);
        for ($idx=0; $idx<count($newGroup2Keys); $idx++) {
            $optionValue = $newGroup2Keys[$idx];
            $optionText = $newGroup2Values[$optionValue];
            $option = $optgroup2OptionTags[$idx];
            $this->tester->assertIsHtmlTag($option,'option',['value' => $optionValue]);
            $values = $option->getChildren();
            $this->assertCount(1, $values);
            $this->assertEquals($optionText, $values[0]);
        }
    }

    public function  testSetValue()
    {
        $namespace = 'ns';
        $name = 'dt';
        $selectOptions = ['true'=>'labelTrue','false'=>'labelFalse', 'filenotfound'=>'labelFileNotFound'];
        $select = ComponentFactory::Select($namespace ,$name, ['foo'=>'bar'])
            ->options($selectOptions);

        $select->setValue('filenotfound');

        $container = $this->tester->getAttributeValue($select, 'componentContainer');
        $this->assertInstanceOf(\Deform\Component\ComponentContainer::class, $container);
        $control = $this->tester->getAttributeValue($container, 'control');
        $this->assertInstanceOf(\Deform\Component\ComponentControls::class, $control);

    }

    public function testSetOptgroupValue()
    {
        $namespace = 'ns';
        $name = 'dt';
        $selectOptions = [
            'group1' => ['a'=>'b','c'=>'d','e'=>'f','g'=>'h'],
            'group2' => ['i'=>'j','k'=>'l']
        ];
        $select = ComponentFactory::Select($namespace ,$name, ['foo'=>'bar'])
            ->optgroupOptions($selectOptions);
        $select->setValue('e');

        $this->expectException(\Exception::class);
        $select->setValue(['k','c','g']);
    }

    public function testHydrate()
    {
        $namespace = 'ns';
        $name = 'dt';
        $selectOptions = ['true'=>'labelTrue','false'=>'labelFalse', 'filenotfound'=>'labelFileNotFound'];
        $select = ComponentFactory::Select($namespace ,$name, ['foo'=>'bar']);
        $this->tester->setAttributeValue($select,'hasOptGroups', false);
        $this->tester->setAttributeValue($select,'options', $selectOptions);
        $select->hydrate();
        //todo:- check it properly!
    }

    public function testHydrateOptgroups() {
        $namespace = 'ns';
        $name = 'dt';
        $selectOptions = [
            'group1' => ['a'=>'b','c'=>'d','e'=>'f','g'=>'h'],
            'group2' => ['i'=>'j','k'=>'l']
        ];
        $select = ComponentFactory::Select($namespace ,$name, ['foo'=>'bar']);
        $this->tester->setAttributeValue($select,'hasOptGroups', true);
        $this->tester->setAttributeValue($select,'options', $selectOptions);
        $select->hydrate();
        //todo:- check it properly!
    }

}
