<?php
namespace Deform\Component;

class ImageTest extends \Codeception\Test\Unit
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
        $name = 'dp';
        $file = ComponentFactory::Image($namespace ,$name, ['foo'=>'bar']);

        $autolabel = $this->tester->getAttributeValue($file, 'autoLabel');
        $this->assertTrue($autolabel);

        $container = $this->tester->getAttributeValue($file, 'componentContainer');
        $this->assertInstanceOf(\Deform\Component\ComponentContainer::class, $container);
        $control = $this->tester->getAttributeValue($container, 'control');
        $this->assertInstanceOf(\Deform\Component\ComponentControls::class, $control);

        $expectedId = 'image-'.$namespace.'-'.$name;
        $this->tester->assertIsHtmlTag($file->input,'input',[
            'id' => $expectedId,
            'name' => $namespace.'['.$name.']',
            'type' => 'file',
        ]);
    }

    public function testSetAccept()
    {
        $namespace = 'ns';
        $name = 'dp';
        $accept = 'image/*';
        $file = ComponentFactory::Image($namespace ,$name, ['foo'=>'bar'])
            ->accept($accept);

        $attributes = $this->tester->getAttributeValue($file->input, 'attributes');
        $this->assertArrayHasKey('accept', $attributes);
        $this->assertEquals($accept, $attributes['accept']);
    }

    public function testHydrate()
    {
        $namespace = 'ns';
        $name = 'dp';
        $accept = 'image/*';
        $file = ComponentFactory::Image($namespace ,$name, ['foo'=>'bar']);
        $this->tester->setAttributeValue($file, 'acceptType', $accept);
        $file->hydrate();
        $attributes = $this->tester->getAttributeValue($file->input, 'attributes');
        $this->assertArrayHasKey('accept', $attributes);
        $this->assertEquals($accept, $attributes['accept']);
    }

    public function testGetHtmlTag()
    {
        $namespace = 'ns';
        $name = 'dp';
        $accept = 'image/*';
        $file = ComponentFactory::Image($namespace ,$name, ['foo'=>'bar']);
        $this->tester->setAttributeValue($file, 'acceptType', $accept);
        $htmlTag = $file->getHtmlTag();
        $this->tester->assertIsHtmlTag($htmlTag, 'div', ['id'=>'ns-dp-container','class'=>'component-container container-type-image']);

        $this->assertFalse($htmlTag->isSelfClosing());
        $childTags = $this->tester->getAttributeValue($htmlTag, 'childTags');
        $this->assertCount(2, $childTags);

        $this->tester->assertIsHtmlTag($childTags[0], 'div', ['class'=>'label-container', 'onclick'=>'return false']);
        $this->tester->assertIsHtmlTag($childTags[1], 'div', ['class'=>'control-container']);

        $labelsTags = $this->tester->getAttributeValue($childTags[0], 'childTags');
        $this->tester->assertIsHtmlTag($labelsTags[0],'label', ['for'=>'image-ns-dp']);
        $labelTextTags = $this->tester->getAttributeValue($labelsTags[0], 'childTags');
        $this->assertCount(1, $labelTextTags);
        $this->assertEquals('dp', $labelTextTags[0]);

        $this->tester->assertIsHtmlTag($labelsTags[1], 'button', ['class'=>'clear-image']);
        $attributes = $this->tester->getAttributeValue($labelsTags[1], 'attributes');
        $this->assertArrayHasKey('style', $attributes);
        $this->assertArrayHasKey('onclick', $attributes);

        $controlsTags = $this->tester->getAttributeValue($childTags[1], 'childTags');
        $this->assertCount(3, $controlsTags);
        $this->tester->assertIsHtmlTag($controlsTags[0],'input', ['id'=>'image-ns-dp','name'=>'ns[dp]','type'=>'file', 'foo'=>'bar']);
        $attributes = $this->tester->getAttributeValue($controlsTags[0], 'attributes');
        $this->assertArrayHasKey('onchange', $attributes);
        $this->assertArrayHasKey('style', $attributes);

        $this->tester->assertIsHtmlTag($controlsTags[1], 'img', ['id'=>'preview-image-ns-dp']);
        $attributes = $this->tester->getAttributeValue($controlsTags[1], 'attributes');
        $this->assertArrayHasKey('src', $attributes);
        $this->assertArrayHasKey('alt', $attributes);
        $this->assertArrayHasKey('style', $attributes);
        $this->assertArrayHasKey('onclick', $attributes);

        $this->tester->assertIsHtmlTag($controlsTags[2], 'input', ['id'=>'hidden-image-ns-dp','type'=>'hidden','name'=>'ns[dp]','value'=>'']);
    }

    public function testSetJavascriptSelectFunction()
    {
        $namespace = 'ns';
        $name = 'dp';
        $accept = 'image/*';
        $image = ComponentFactory::Image($namespace ,$name, ['foo'=>'bar']);
        $image->accept($accept);
        $js = <<<JS
function(event) {
    
}
JS;

        $image->setJavascriptSelectFunction($js);
        $js = $this->tester->getAttributeValue($image, 'javascriptSelectFunction');
    }

    public function testShadowJavascript()
    {
        $namespace = 'ns';
        $name = 'dp';
        $accept = 'image/*';
        $file = ComponentFactory::Image($namespace ,$name, ['foo'=>'bar']);
        $this->tester->setAttributeValue($file, 'acceptType', $accept);
        $shadowJs = $file->shadowJavascript();
        $this->assertArrayHasKey('.control-container input#hidden-image-namespace-name', $shadowJs);
        $this->assertArrayHasKey('.control-container input', $shadowJs);
    }
}
