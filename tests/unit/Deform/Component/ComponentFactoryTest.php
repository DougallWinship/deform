<?php
namespace Deform\Component;

use Deform\Form\Form;

class ComponentFactoryTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function testIdentifyComponents()
    {
        $expectedComponents = [
            "Button",
            "Checkbox",
            "CheckboxMulti",
            "Currency",
            "Date",
            "DateTime",
            "Display",
            "Email",
            "File",
            "MultipleFile",
            "Hidden",
            "InputButton",
            "Password",
            "RadioButtonSet",
            "Select",
            "SelectMulti",
            "Slider",
            "Submit",
            "Text",
            "TextArea",
        ];
        foreach ($expectedComponents as $component) {
            $this->assertTrue(ComponentFactory::isRegisteredComponent($component));
        }
    }

    public function testBuildViaCallStatic()
    {
        $button = ComponentFactory::Button("namespace","button");
        $this->assertInstanceOf(Button::class, $button);
    }

    public function testBuildNonExistentViaCallStatic()
    {
        $this->expectException(\Exception::class);
        $button = ComponentFactory::Potato("namespace","button");
    }

    public function testBuildWithoutNamespace()
    {
        $button = ComponentFactory::build("Button", "namespace","button");
        $this->assertInstanceOf(Button::class, $button);
    }

    public function testBuildWithNamespace()
    {
        $button = ComponentFactory::build(Button::class, "namespace","button");
        $this->assertInstanceOf(Button::class, $button);
    }

    public function testBuildWithBadClassName()
    {
        $this->expectException(\Exception::class);
        ComponentFactory::build("NotAThing", "namespace","button");
    }

    public function testBuildWithBaseComponent()
    {
        $this->expectException(\Exception::class);
        ComponentFactory::build(BaseComponent::class, "namespace","button");
    }

    public function testBuildWithWrongNamespace()
    {
        $this->expectException(\Exception::class);
        ComponentFactory::build(Form::class, "namespace","button");
    }

    public function testBuildInvalidClass()
    {
        $this->expectException(\Exception::class);
        ComponentFactory::build(ComponentControls::class, "namespace","button");
    }
}