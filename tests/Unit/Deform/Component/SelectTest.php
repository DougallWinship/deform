<?php
namespace App\Tests\Unit\Deform\Component;

use Deform\Component\ComponentFactory;
use Deform\Exception\DeformComponentException;

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

    public function testInvalidSetupNull()
    {
        $select = ComponentFactory::Select('ns','sl');
        $this->expectException(DeformComponentException::class);
        $this->expectExceptionMessage('Select component options must be an array');
        $select->getHtmlTag();
    }

    public function testInvalidSetupEmpty()
    {
        $select = ComponentFactory::Select('ns','sl');
        $select->options([]);
        $this->expectException(DeformComponentException::class);
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

        $container = $this->tester->getAttributeValue($select, 'componentContainer');
        $this->assertInstanceOf(\Deform\Component\ComponentContainer::class, $container);
        $control = $this->tester->getAttributeValue($container, 'control');
        $this->assertInstanceOf(\Deform\Component\ComponentControls::class, $control);

        $inputControls = $this->tester->getAttributeValue($control,'controlTags');
        $this->assertCount(1, $inputControls);
        $selectTag = $inputControls[0];

        $this->checkOptions($selectTag->getChildren(), $newSelectOptions);
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

        $this->checkOptgroups($selectGroups, $newSelectOptions);
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

        $this->expectException(DeformComponentException::class);
        $select->setValue(['k','c','g']);
    }

    public function testHydrate()
    {
        $namespace = 'ns';
        $name = 'dt';
        $selectOptions = ['true'=>'labelTrue','false'=>'labelFalse', 'filenotfound'=>'labelFileNotFound'];
        $select = ComponentFactory::Select($namespace ,$name, ['foo'=>'bar']);
        $this->tester->setAttributeValue($select,'hasOptGroups', false);
        $this->tester->setAttributeValue($select,'optionsValues', $selectOptions);
        $select->hydrate();

        $container = $this->tester->getAttributeValue($select, 'componentContainer');
        $this->assertInstanceOf(\Deform\Component\ComponentContainer::class, $container);
        $control = $this->tester->getAttributeValue($container, 'control');
        $this->assertInstanceOf(\Deform\Component\ComponentControls::class, $control);

        $inputControls = $this->tester->getAttributeValue($control,'controlTags');
        $this->assertCount(1, $inputControls);
        $selectTag = $inputControls[0];

        $this->checkOptions($selectTag->getChildren(), $selectOptions);
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
        $this->tester->setAttributeValue($select,'optionsValues', $selectOptions);
        $select->hydrate();

        $container = $this->tester->getAttributeValue($select, 'componentContainer');
        $this->assertInstanceOf(\Deform\Component\ComponentContainer::class, $container);
        $control = $this->tester->getAttributeValue($container, 'control');
        $this->assertInstanceOf(\Deform\Component\ComponentControls::class, $control);
        $inputControls = $this->tester->getAttributeValue($control,'controlTags');
        $this->assertCount(1, $inputControls);
        $selectTag = $inputControls[0];
        $selectGroups = $selectTag->getChildren();

        $this->checkOptgroups($selectGroups, $selectOptions);
    }

    /**
     * @param array $optionsTags
     * @param array $optionsData
     */
    private function checkOptions(array $optionsTags, array $optionsData)
    {
        $optionsDataKeys = array_keys($optionsData);
        $this->assertSameSize($optionsTags, $optionsDataKeys);
        for($idx=0; $idx<count($optionsData); $idx++) {
            $optionTag = $optionsTags[$idx];
            $optionValue = $optionsDataKeys[$idx];
            $optionText = $optionsData[$optionValue];
            $this->tester->assertIsHtmlTag($optionTag,'option', ['value' => $optionValue]);
            $values = $optionTag->getChildren();
            $this->assertCount(1, $values);
            $this->assertEquals($optionText, $values[0]);
        }
    }

    /**
     * @param array $selectOptgroupTags
     * @param array $optgroupData
     */
    private function checkOptgroups(array $selectOptgroupTags, array $optgroupData)
    {
        $optgroupDataKeys = array_keys($optgroupData);
        for($groupIdx=0; $groupIdx<count($optgroupDataKeys); $groupIdx++) {
            $optgroupSectionName = $optgroupDataKeys[$groupIdx];
            $optgroupSectionData =  $optgroupData[$optgroupSectionName];
            $optgroupSectionDataKeys = array_keys($optgroupSectionData);
            $optgroupTag = $selectOptgroupTags[$groupIdx];
            $optgroupTagOptions = $optgroupTag->getChildren();
            $this->assertSameSize($optgroupTagOptions, $optgroupSectionDataKeys);
            for ($idx=0; $idx<count($optgroupSectionDataKeys); $idx++) {
                $optionValue = $optgroupSectionDataKeys[$idx];
                $optionText = $optgroupSectionData[$optionValue];
                $optgroupTagOption = $optgroupTagOptions[$idx];
                $this->tester->assertIsHtmlTag($optgroupTagOption,'option', ['value' => $optionValue]);
                $values = $optgroupTagOption->getChildren();
                $this->assertCount(1, $values);
                $this->assertEquals($optionText, $values[0]);
            }
        }
    }

}
