<?php
namespace Deform\Component;

use Deform\Exception\DeformComponentException;
use Deform\Html\Html;

class ComponentControlsTest extends \Codeception\Test\Unit
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
    public function testAddControlMissingId()
    {
        $this->expectException(DeformComponentException::class);
        $componentControls = new ComponentControls();
        $badControlTag = Html::div();
        $componentControls->addControl($badControlTag);
    }

    public function testAddControlUndecoratedGoodControl()
    {
        $componentControls = new ComponentControls();
        $badControlTag = Html::input(['id'=>'foo','name'=>'bar']);
        $componentControls->addControl($badControlTag);
        $controlTags = $this->tester->getAttributeValue($componentControls, 'controlTags');
        $this->assertCount(1, $controlTags);
        $this->assertEquals($badControlTag, $controlTags[0]);
        $allTags = $this->tester->getAttributeValue($componentControls, 'controlTags');
        $this->assertCount(1, $allTags);
        $this->assertEquals($badControlTag, $allTags[0]);
        $tagsWithForById =  $this->tester->getAttributeValue($componentControls, 'tagsWithForById');
        $this->assertCount(0, $tagsWithForById);
    }

    public function testAddControlDecoratedIncludesSelf()
    {
        $componentControls = new ComponentControls();
        $controlTag = Html::input(['id'=>'foo','name'=>'bar']);
        $decorations = [
            'foo',
            Html::br(),
            $controlTag
        ];
        $componentControls->addControl($controlTag, $decorations);
    }

    // debatable this one, but the overhead of recursing all decorations at every runtime is a bit silly
    public function testAddControlDecoratedNestedIncludesSelfFail()
    {
        $componentControls = new ComponentControls();
        $id = 'foo';
        $controlTag = Html::input(['id'=>$id,'name'=>'bar']);
        $labelWithFor = Html::label(['for'=>$id]);
        $decorations = [
            $labelWithFor,
            Html::br(),
            Html::div()->add($controlTag) // so it *does* contain itself but it's nested
        ];
        $this->expectException(DeformComponentException::class);
        $componentControls->addControl($controlTag, $decorations);
        $tagsWithForById = $this->tester->getAttributeValue($componentControls, 'tagsWithForById');
        $expected = [
            $id=>[$labelWithFor]
        ];
        $this->assertEquals($expected, $tagsWithForById);
    }

    public function testAddControlDecoratedWithFor()
    {
        $componentControls = new ComponentControls();
        $id = 'foo';
        $controlTag = Html::input(['id'=>$id,'name'=>'bar']);
        $labelWithFor = Html::label(['for'=>$id]);
        $decorations = [
            $labelWithFor,
            Html::br(),
            $controlTag
        ];
        $componentControls->addControl($controlTag, $decorations);
        $tagsWithForById = $this->tester->getAttributeValue($componentControls, 'tagsWithForById');
        $expected = [
            $id=>[$labelWithFor]
        ];
        $this->assertEquals($expected, $tagsWithForById);
    }

    public function testAddControlDecoratedWithForMultiple()
    {
        $componentControls = new ComponentControls();
        $id = 'foo';
        $controlTag = Html::input(['id'=>$id,'name'=>'bar']);
        $labelWithFor = Html::label(['for'=>$id]);
        $decorations = [
            Html::br(),
            $labelWithFor,
            $controlTag
        ];
        $componentControls->addControl($controlTag, $decorations);

        $id2 = 'foo2';
        $controlTag2 = Html::input(['id'=>$id2,'name'=>'bar2']);
        $labelWithFor2 = Html::label(['for'=>$id2]);
        $labelWithFor2b = Html::label(['for'=>$id2]);
        $decorations2 = [
            $labelWithFor2,
            Html::br(),
            $controlTag2,
            $labelWithFor2b,
        ];
        $componentControls->addControl($controlTag2, $decorations2);

        $tagsWithForById = $this->tester->getAttributeValue($componentControls, 'tagsWithForById');
        $expected = [
            $id => [$labelWithFor],
            $id2 => [$labelWithFor2, $labelWithFor2b]
        ];
        $this->assertEquals($expected, $tagsWithForById);
    }


    public function testChangeNamespacedAttributesIrrelevant()
    {
        $componentControls = new ComponentControls();
        $id = 'foo';
        $controlTag = Html::input(['id'=>$id,'name'=>'bar']);
        $labelWithFor = Html::label();
        $decorations = [
            Html::br(),
            $labelWithFor,
            $controlTag
        ];
        $componentControls->addControl($controlTag, $decorations);
        $componentControls->changeNamespacedAttributes('newid','newname');
        $attributes = $this->tester->getAttributeValue($controlTag, 'attributes');
        $this->assertEquals('newid', $attributes['id']);
        $this->assertEquals('newname', $attributes['name']);
        $this->assertEquals($decorations, $componentControls->getHtmlTags());
    }

    public function testChangeNamespacedAttributesSingleRelevant()
    {
        $componentControls = new ComponentControls();
        $id = 'foo';
        $controlTag = Html::input(['id'=>$id,'name'=>'bar']);
        $labelWithFor = Html::label(['for'=>$id]);
        $decorations = [
            Html::br(),
            $labelWithFor,
            $controlTag
        ];
        $componentControls->addControl($controlTag, $decorations);
        $componentControls->changeNamespacedAttributes('newid','newname');
        $attributes = $this->tester->getAttributeValue($controlTag, 'attributes');
        $this->assertEquals('newid', $attributes['id']);
        $this->assertEquals('newname', $attributes['name']);
        $attributes = $this->tester->getAttributeValue($labelWithFor, 'attributes');
        $this->assertEquals('newid', $attributes['for']);
    }

    public function testChangeNamespacedAttributesMultipleRelevantMissingValues()
    {
        $componentControls = new ComponentControls();
        $id = 'foo';
        $controlTag = Html::input(['id'=>$id,'name'=>'bar']);
        $labelWithFor = Html::label(['for'=>$id]);
        $decorations = [
            Html::br(),
            $labelWithFor,
            $controlTag
        ];
        $componentControls->addControl($controlTag, $decorations);

        $id2 = 'foo2';
        $controlTag2 = Html::input(['id'=>$id2,'name'=>'bar2']);
        $labelWithFor2 = Html::label(['for'=>$id2]);
        $labelWithFor2b = Html::label(['for'=>$id2]);
        $decorations2 = [
            $labelWithFor2,
            Html::br(),
            $controlTag2,
            $labelWithFor2b,
        ];
        $componentControls->addControl($controlTag2, $decorations2);

        $this->expectException(DeformComponentException::class);
        $this->expectExceptionMessage("When there are multiple control tags they must specify a value");
        $componentControls->changeNamespacedAttributes('newid','newname');
    }

    public function testChangeNamespacedAttributesMultipleRelevant()
    {
        $sharedName = "fieldname[]";
        $componentControls = new ComponentControls();
        $id = 'foo';
        $controlTagValue = "value1";
        $controlTag = Html::input(['id'=>$id,'name'=>$sharedName,'value'=>$controlTagValue]);
        $labelWithFor = Html::label(['for'=>$id]);
        $decorations = [
            Html::br(),
            $labelWithFor,
            $controlTag
        ];
        $componentControls->addControl($controlTag, $decorations);

        $id2 = 'foo2';
        $controlTagValue2 = "value2";
        $controlTag2 = Html::input(['id'=>$id2,'name'=>$sharedName,'value'=>$controlTagValue2]);
        $labelWithFor2 = Html::label(['for'=>$id2]);
        $labelWithFor2b = Html::label(['for'=>$id2]);
        $decorations2 = [
            $labelWithFor2,
            Html::br(),
            $controlTag2,
            $labelWithFor2b,
        ];
        $componentControls->addControl($controlTag2, $decorations2);

        $expectedName = 'newname[]';
        $componentControls->changeNamespacedAttributes('newid',$expectedName);
        $attributes = $this->tester->getAttributeValue($controlTag, 'attributes');
        $expectedId = BaseComponent::getMultiControlId('newid', $controlTagValue);
        $this->assertEquals($expectedId, $attributes['id']);
        $this->assertEquals($expectedName, $attributes['name']);
        $attributes = $this->tester->getAttributeValue($labelWithFor, 'attributes');
        $this->assertEquals($expectedId, $attributes['for']);

        $attributes = $this->tester->getAttributeValue($controlTag2, 'attributes');
        $expectedId = BaseComponent::getMultiControlId('newid', $controlTagValue2);
        $this->assertEquals($expectedId, $attributes['id']);
        $this->assertEquals($expectedName, $attributes['name']);
        $attributes = $this->tester->getAttributeValue($labelWithFor2, 'attributes');
        $this->assertEquals($expectedId, $attributes['for']);
        $attributes = $this->tester->getAttributeValue($labelWithFor2b, 'attributes');
        $this->assertEquals($expectedId, $attributes['for']);
    }

    public function testGetControlsMultiple()
    {
        $sharedName = "fieldname[]";
        $componentControls = new ComponentControls();
        $id = 'foo';
        $controlTagValue = "value1";
        $controlTag = Html::input(['id'=>$id,'name'=>$sharedName,'value'=>$controlTagValue]);
        $labelWithFor = Html::label(['for'=>$id]);
        $decorations = [
            Html::br(),
            $labelWithFor,
            $controlTag
        ];
        $componentControls->addControl($controlTag, $decorations);

        $id2 = 'foo2';
        $controlTagValue2 = "value2";
        $controlTag2 = Html::input(['id'=>$id2,'name'=>$sharedName,'value'=>$controlTagValue2]);
        $labelWithFor2 = Html::label(['for'=>$id2]);
        $labelWithFor2b = Html::label(['for'=>$id2]);
        $decorations2 = [
            $labelWithFor2,
            Html::br(),
            $controlTag2,
            $labelWithFor2b,
        ];
        $componentControls->addControl($controlTag2, $decorations2);

        $controls = $componentControls->getControls();
        $this->assertIsArray($controls);
        $this->assertArrayHasKey(0, $controls);
        $this->assertArrayHasKey(1, $controls);
        $this->assertEquals($controlTag, $controls[0]);
        $this->assertEquals($controlTag2, $controls[1]);
    }

    public function testSetValueWrongNumberOfControls()
    {
        $sharedName = "fieldname[]";
        $componentControls = new ComponentControls();
        $id = 'foo';
        $controlTagValue = "value1";
        $controlTag = Html::input(['id'=>$id,'name'=>$sharedName,'value'=>$controlTagValue]);
        $labelWithFor = Html::label(['for'=>$id]);
        $decorations = [
            Html::br(),
            $labelWithFor,
            $controlTag
        ];
        $componentControls->addControl($controlTag, $decorations);

        $id2 = 'foo2';
        $controlTagValue2 = "value2";
        $controlTag2 = Html::input(['id'=>$id2,'name'=>$sharedName,'value'=>$controlTagValue2]);
        $labelWithFor2 = Html::label(['for'=>$id2]);
        $labelWithFor2b = Html::label(['for'=>$id2]);
        $decorations2 = [
            $labelWithFor2,
            Html::br(),
            $controlTag2,
            $labelWithFor2b,
        ];
        $componentControls->addControl($controlTag2, $decorations2);

        $this->expectException(DeformComponentException::class);
        $componentControls->setValue([1,2,3]);
    }

    public function testSetValueSingle()
    {
        $componentControls = new ComponentControls();
        $id = 'foo';
        $controlTagValue = "value1";
        $controlTag = Html::input(['id'=>$id,'name'=>$id,'value'=>$controlTagValue]);
        $labelWithFor = Html::label(['for'=>$id]);
        $decorations = [
            Html::br(),
            $labelWithFor,
            $controlTag
        ];
        $componentControls->addControl($controlTag, $decorations);
        $componentControls->setValue('bar');

        $this->assertEquals('bar', $controlTag->get('value'));
    }

    public function testSetValueMultiple()
    {
        $sharedName = "fieldname[]";
        $componentControls = new ComponentControls();
        $id = 'foo';
        $controlTagValue = "value1";
        $controlTag = Html::input(['id'=>$id,'name'=>$sharedName,'value'=>$controlTagValue]);
        $labelWithFor = Html::label(['for'=>$id]);
        $decorations = [
            Html::br(),
            $labelWithFor,
            $controlTag
        ];
        $componentControls->addControl($controlTag, $decorations);

        $id2 = 'foo2';
        $controlTagValue2 = "value2";
        $controlTag2 = Html::input(['id'=>$id2,'name'=>$sharedName,'value'=>$controlTagValue2]);
        $labelWithFor2 = Html::label(['for'=>$id2]);
        $labelWithFor2b = Html::label(['for'=>$id2]);
        $decorations2 = [
            $labelWithFor2,
            Html::br(),
            $controlTag2,
            $labelWithFor2b,
        ];
        $componentControls->addControl($controlTag2, $decorations2);

        $componentControls->setValue(['aaa','bbb']);
        $this->assertEquals('aaa', $controlTag->get('value'));
        $this->assertEquals('bbb', $controlTag2->get('value'));
    }
}