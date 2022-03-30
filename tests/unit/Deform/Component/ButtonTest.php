<?php
namespace Deform\Component;

use Deform\Component\Button;
use Deform\Component\ComponentFactory;

class ButtonTest extends \Codeception\Test\Unit
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
    public function testSetup()
    {
        $button = ComponentFactory::Button('namespace','button', ['foo'=>'bar']);
        $autolabel = $this->tester->getAttributeValue($button, 'autoLabel');
        $this->assertFalse($autolabel);
        $this->assertInstanceOf(\Deform\Html\HtmlTag::class, $button->button);

        $container = $this->tester->getAttributeValue($button, 'componentContainer');
        $this->assertInstanceOf(\Deform\Component\ComponentContainer::class, $container);
        $control = $this->tester->getAttributeValue($container, 'control');
        $this->assertInstanceOf(\Deform\Component\ComponentControls::class, $control);
    }

    public function testHtml()
    {
        $button = ComponentFactory::Button('namespace','button', ['foo'=>'bar']);
        $html = "<span>Contents of Button</span>";
        $button->html($html);
        $value = $this->tester->getAttributeValue($button, 'buttonHtml');
        $this->assertEquals($html, $value);
        $buttonTag = $this->tester->getAttributeValue($button, 'button');
        $childTags = $this->tester->getAttributeValue($buttonTag,'childTags');
        $this->assertCount(1, $childTags);
        $this->assertEquals($html, $childTags[0]);

        $newHtml = "<p>New Contents</p>";
        $button->html($newHtml);
        $value = $this->tester->getAttributeValue($button, 'buttonHtml');
        $this->assertEquals($newHtml, $value);
        $buttonTag = $this->tester->getAttributeValue($button, 'button');
        $childTags = $this->tester->getAttributeValue($buttonTag,'childTags');
        $this->assertCount(1, $childTags);
        $this->assertEquals($newHtml, $childTags[0]);
    }

    public function testType()
    {
        $button = ComponentFactory::Button('namespace', 'button', ['foo' => 'bar']);
        $validButtonType = Button::VALID_BUTTON_TYPES;
        foreach ($validButtonType as $type) {
            $button->type($type);
            $value = $this->tester->getAttributeValue($button, 'buttonType');
            $this->assertEquals($type, $value);
            $buttonTag = $this->tester->getAttributeValue($button,'button');
            $buttonAttributes = $this->tester->getAttributeValue($buttonTag,'attributes');
            $this->assertArrayHasKey('type', $buttonAttributes);
            $this->assertEquals($type, $buttonAttributes['type']);
        }
    }

    public function testInvalidType()
    {
        $button = ComponentFactory::Button('namespace', 'button', ['foo' => 'bar']);
        $this->expectException(\Exception::class);
        $button->type('invalidType');
    }

    public function testHydrate()
    {
        $button = ComponentFactory::Button('namespace', 'button', ['foo' => 'bar']);
        $type = 'submit';
        $html = '<span>button html</span>';
        $this->tester->setAttributeValue($button, 'buttonType', $type);
        $this->tester->setAttributeValue($button, 'buttonHtml', $html);
        $button->hydrate();

        $buttonTag = $this->tester->getAttributeValue($button,'button');
        $buttonAttributes = $this->tester->getAttributeValue($buttonTag,'attributes');
        $this->assertArrayHasKey('type', $buttonAttributes);
        $this->assertEquals($type, $buttonAttributes['type']);
        $childTags = $this->tester->getAttributeValue($buttonTag,'childTags');
        $this->assertCount(1, $childTags);
        $this->assertEquals($html, $childTags[0]);
    }
}
