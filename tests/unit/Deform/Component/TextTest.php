<?php
namespace Deform\Component;

class TextTest extends \Codeception\Test\Unit
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
        $name = 'tx';
        $text = ComponentFactory::Text($namespace ,$name, ['foo'=>'bar']);

        $autolabel = $this->tester->getAttributeValue($text, 'autoLabel');
        $this->assertTrue($autolabel);

        $container = $this->tester->getAttributeValue($text, 'componentContainer');
        $this->assertInstanceOf(\Deform\Component\ComponentContainer::class, $container);
        $control = $this->tester->getAttributeValue($container, 'control');
        $this->assertInstanceOf(\Deform\Component\ComponentControls::class, $control);

        $expectedId = 'text-'.$namespace.'-'.$name;
        $this->tester->assertIsHtmlTag($text->input,'input',[
            'id' => $expectedId,
            'name' => $namespace.'['.$name.']',
            'type' => 'text',
        ]);
    }

    public function testDatalistSetup()
    {
        $namespace = 'ns';
        $name = 'tx';
        $datalistValues = ['one','two', 'three'];
        $text = ComponentFactory::Text($namespace ,$name, ['foo'=>'bar'])
            ->datalist($datalistValues);

        $container = $this->tester->getAttributeValue($text, 'componentContainer');
        $this->assertInstanceOf(\Deform\Component\ComponentContainer::class, $container);
        $control = $this->tester->getAttributeValue($container, 'control');
        $this->assertInstanceOf(\Deform\Component\ComponentControls::class, $control);


        $expectedId = 'text-'.$namespace.'-'.$name;
        $expectedName =  $namespace.'['.$name.']';
        $this->tester->assertIsHtmlTag($text->input,'input',[
            'id' => $expectedId,
            'name' => $expectedName,
            'type' => 'text',
        ]);

        $controlTags = $this->tester->getAttributeValue($control, 'controlTags');
        $allControlTags = $this->tester->getAttributeValue($control, 'allTags');
        $this->assertCount(2,$controlTags);
        $this->assertCount(2, $allControlTags);
        $this->assertEquals($controlTags, $allControlTags);
        list($input, $datalist) = $controlTags;
        $this->assertEquals($text->input,$input);

        $this->tester->assertIsHtmlTag($datalist, 'datalist', ['id' => $expectedName.'-datalist']);
        $childTags = $datalist->getChildren();
        $this->assertSameSize($datalistValues, $childTags);
        for ($idx=0; $idx<count($datalistValues); $idx++) {
            $this->tester->assertIsHtmlTag($childTags[$idx],'option',['value'=>$datalistValues[$idx]]);
        }
    }

    public function testDatalistWithIdSetup()
    {
        $namespace = 'ns';
        $name = 'tx';
        $datalistId = 'test-datalist-id';
        $datalistValues = ['one','two', 'three'];
        $text = ComponentFactory::Text($namespace ,$name, ['foo'=>'bar'])
            ->datalist($datalistValues, $datalistId);
        $container = $this->tester->getAttributeValue($text, 'componentContainer');
        $this->assertInstanceOf(\Deform\Component\ComponentContainer::class, $container);
        $control = $this->tester->getAttributeValue($container, 'control');
        $this->assertInstanceOf(\Deform\Component\ComponentControls::class, $control);
        $controlTags = $this->tester->getAttributeValue($control, 'controlTags');
        list($input, $datalist) = $controlTags;
        $this->tester->assertIsHtmlTag($datalist, 'datalist', ['id' => $datalistId]);
    }


    public function testHydrate()
    {
        $namespace = 'ns';
        $name = 'tx';
        $datalistId = 'test-datalist-id';
        $datalistValues = ['one','two', 'three'];
        $text = ComponentFactory::Text($namespace ,$name, ['foo'=>'bar']);
        $this->tester->setAttributeValue($text, 'datalist', $datalistValues);
        $this->tester->setAttributeValue($text, 'datalistId', $datalistId);
        $text->hydrate();
    }
}
