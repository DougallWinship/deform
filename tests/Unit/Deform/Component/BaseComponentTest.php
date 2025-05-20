<?php
namespace App\Tests\Unit\Deform\Component;

use Deform\Component\ComponentFactory;
use Deform\Exception\DeformComponentException;

/**
 * it appears to be tricky to mock an abstract class with a protected constructor (if you read this and know of a way
 * then let me know), so instead we'll use concrete instances using the ComponentFactory.
 */
class BaseComponentTest extends \Codeception\Test\Unit
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

    public function testTooltip()
    {
        $input = ComponentFactory::Text('ns','field');
        $tooltip = 'this is the tooltip';
        $input->tooltip('this is the tooltip');

        $inputTag = $input->getHtmlTag();
        $attributes = $this->tester->getAttributeValue($inputTag, 'attributes');

        $this->assertArrayHasKey('title',$attributes);
        $this->assertEquals($tooltip, $attributes['title']);

    }

    public function testLabel()
    {
        $namespace = 'ns';
        $field = 'field';
        $input = ComponentFactory::Text($namespace,$field);
        $label = 'customise the label';
        $input->label($label);

        $inputTag = $input->getHtmlTag();

        $inputTagChildren = $inputTag->getChildren();
        $this->assertCount(2, $inputTagChildren);
        list($labelWrapper, $controlWrapper)  = $inputTagChildren;

        $this->tester->assertIsHtmlTag($labelWrapper, 'div', ['class'=>'label-container']);
        $this->tester->assertIsHtmlTag($controlWrapper, 'div', ['class'=>'control-container']);

        $labelWrapperChildren = $labelWrapper->getChildren();
        $this->assertCount(1, $labelWrapperChildren);

        $labelTag = $labelWrapperChildren[0];
        $this->tester->assertIsHtmlTag($labelTag, 'label', ['for'=>'text-'.$namespace.'-'.$field]);
    }

    public function testHint()
    {
        $namespace = 'ns';
        $field = 'field';
        $input = ComponentFactory::Text($namespace,$field);
        $hint = 'some sort of hint';
        $input->hint($hint);

        $inputTag = $input->getHtmlTag();
        $inputTagChildren = $inputTag->getChildren();
        $this->assertCount(3, $inputTagChildren);

        list($labelWrapper, $inputWrapper, $hintTag) = $inputTagChildren;

        $this->tester->assertIsHtmlTag($labelWrapper, 'div', ['class'=>'label-container']);
        $this->tester->assertIsHtmlTag($inputWrapper, 'div', ['class'=>'control-container']);
        $this->tester->assertIsHtmlTag($hintTag, 'div', ['class'=>'hint-container']);

        $hintTagChildren = $hintTag->getChildren();
        $this->assertCount(1,$hintTagChildren);
        $this->assertEquals($hint, $hintTagChildren[0]);
    }

    public function testSetError()
    {
        $namespace = 'ns';
        $field = 'field';
        $input = ComponentFactory::Text($namespace,$field);
        $error = 'this is an error message';
        $input->setError($error);

        $inputTag = $input->getHtmlTag();
        $inputTagChildren = $inputTag->getChildren();
        $this->assertCount(3, $inputTagChildren);

        list($labelWrapper, $inputWrapper, $errorTag) = $inputTagChildren;
        $this->tester->assertIsHtmlTag($labelWrapper, 'div',  ['class'=>'label-container']);
        $this->tester->assertIsHtmlTag($inputWrapper, 'div', ['class'=>'control-container']);
        $this->tester->assertIsHtmlTag($errorTag, 'div', ['class'=>'error-container']);

        $hintTagChildren = $errorTag->getChildren();
        $this->assertCount(1,$hintTagChildren);
        $this->assertEquals($error, $hintTagChildren[0]);
    }

    public function testSetValue()
    {
        $namespace = 'ns';
        $field = 'field';
        $input = ComponentFactory::Text($namespace,$field);

        $value = 'something or other';
        $input->setValue($value);

        $container = $this->tester->getAttributeValue($input, 'componentContainer');
        $this->assertInstanceOf(\Deform\Component\ComponentContainer::class, $container);
        $control = $this->tester->getAttributeValue($container, 'control');
        $this->assertInstanceOf(\Deform\Component\ComponentControls::class, $control);

        $inputControls = $this->tester->getAttributeValue($control,'controlTags');
        $this->assertCount(1, $inputControls);
        $inputTag = $inputControls[0];
        $this->assertEquals($input->input, $inputTag);
        $attributes = $this->tester->getAttributeValue($inputTag,'attributes');

        $this->assertArrayHasKey('value', $attributes);
        $this->assertEquals($value, $attributes['value']);
    }

    public function testSetNamespace()
    {
        $namespace = 'ns';
        $field = 'field';
        $input = ComponentFactory::Text($namespace,$field);

        $newNamespace = 'newns';
        $input->setNamespace($newNamespace);

        $inputTag = $input->getHtmlTag();
        $this->tester->assertIsHtmlTag($inputTag, 'div', [
            'id' => $newNamespace.'-'.$field.'-container',
            'class' => 'component-container container-type-text',
        ]);

        $inputTagChildren = $inputTag->getChildren();
        $this->assertCount(2, $inputTagChildren);

        list($labelWrapper, $inputWrapper) = $inputTagChildren;
        $this->tester->assertIsHtmlTag($labelWrapper, 'div',  ['class'=>'label-container']);
        $this->tester->assertIsHtmlTag($inputWrapper, 'div', ['class'=>'control-container']);

        $inputId = 'text-'.$newNamespace.'-'.$field;

        $labelWrapperChildren = $labelWrapper->getChildren();
        $this->assertCount(1, $labelWrapperChildren);
        list($labelTag) = $labelWrapperChildren;
        $this->tester->assertIsHtmlTag($labelTag,'label',['for' => $inputId]);

        $inputWrapperChildren = $inputWrapper->getChildren();
        $this->assertCount(1, $inputWrapperChildren);
        list($inputTag) = $inputWrapperChildren;
        $this->tester->assertIsHtmlTag($inputTag, 'input', [
            'id' => $inputId,
            'name' => $newNamespace.'['.$field.']',
            'type' => 'text'
        ]);
    }

    public function testToString()
    {
        $namespace = 'ns';
        $field = 'field';
        $input = ComponentFactory::Text($namespace, $field);
        $inputTag = $input->getHtmlTag();
        // just checking that BaseComponent::_toString() delegates to the generated HtmlTag::_toString()
        $this->assertEquals((string)$inputTag, (string)$input->__toString());
    }

    public function testToStringException()
    {
        $this->expectException(DeformComponentException::class);
        $namespace = 'ns';
        $field = 'field';
        $input = ComponentFactory::Text($namespace, $field);
        $container = $this->tester->getAttributeValue($input, 'componentContainer');
        $control = $this->tester->getAttributeValue($container, 'control');
        // force __toString to throw an exception by emptying the component's control tags!
        $this->tester->setAttributeValue($control, 'controlTags', []);
        $input->__toString();
    }

    public function testFindNodesTag()
    {
        $options = [1,2,3,4,5,'a','b','c'];
        $select = ComponentFactory::Select('ns','field')
            ->options($options);

        $nodes = $select->findNodes('option');
        $this->assertSameSize($nodes, $options);
        for($idx=0; $idx<count($options); $idx++) {
            $this->tester->assertIsHtmlTag($nodes[$idx],'option',['value'=>$options[$idx]]);
        }
    }

    public function testFindNodesClass()
    {
        $checkboxes = [1,2,3,4,5,'a','b','c'];
        $checkboxMulti = ComponentFactory::CheckboxMulti('ns','field')
            ->checkboxes($checkboxes);

        $checkboxMultiTag = $checkboxMulti->getHtmlTag();
        $findClass = "checkboxmulti-checkbox-wrapper";
        $nodes = $checkboxMultiTag->findNodes('.'.$findClass);
        $this->assertSameSize($checkboxes, $nodes);
        for ($idx=0; $idx<count($checkboxes); $idx++) {
            $checkBoxWrapperTag = $nodes[$idx];
            $this->tester->assertIsHtmlTag($checkBoxWrapperTag,'div',['class'=>$findClass]);
        }
    }

    public function testFindNodesId()
    {
        $options = [1,2,3,4,5];
        $namespace = 'ns';
        $field = 'sl';
        $select = ComponentFactory::Select($namespace,$field)
            ->options($options);
        $selectTag = $select->getHtmlTag();
        $findId = 'select-'.$namespace.'-'.$field;
        $nodes = $selectTag->findNodes('#'.$findId);

        $this->assertCount(1, $nodes);
        list($selectTag) = $nodes;
        $this->tester->assertIsHtmlTag($selectTag, 'select', ['id'=>$findId]);
    }

    public function testRequiresMultiFormEncoding()
    {
        $text = ComponentFactory::Text('ns','txt');
        $this->assertFalse($text->requiresMultiformEncoding());
        $file = ComponentFactory::File('ns','fl');
        $this->assertTrue($file->requiresMultiformEncoding());
    }

    public function testDumbMagicCall()
    {
        $text = ComponentFactory::Text('ns','txt');
        $this->expectException(DeformComponentException::class);
        $text->wibble(1,2,3);
    }

    public function testToArray()
    {
        $field = 'txt';
        $text = ComponentFactory::Text('ns',$field);
        $textComponentArray = $text->toArray();
        $this->assertArrayHasKey('class', $textComponentArray);
        $this->assertEquals(\Deform\Component\Text::class, $textComponentArray['class']);
        $this->assertArrayHasKey('name', $textComponentArray);
        $this->assertEquals($field, $textComponentArray['name']);

        // todo - this could be much better!!
    }
}