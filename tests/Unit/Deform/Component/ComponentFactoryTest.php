<?php
namespace App\Tests\Unit\Deform\Component;

use Deform\Component\BaseComponent;
use Deform\Component\Button;
use Deform\Component\ComponentControls;
use Deform\Exception\DeformComponentException;
use Deform\Form\FormModel;
use Deform\Component\ComponentFactory;

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
            "ColorSelector",
            "Currency",
            "Date",
            "DateTime",
            "Display",
            "Email",
            "File",
            "Hidden",
            "Image",
            "MultipleEmail",
            "MultipleFile",
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
            $this->assertTrue(ComponentFactory::isRegisteredComponent($component),"is registered component : ".$component);
        }
    }

    public function testBuildViaCallStatic()
    {
        $button = ComponentFactory::Button("namespace","button");
        $this->assertInstanceOf(Button::class, $button);
    }

    public function testBuildNonExistentViaCallStatic()
    {
        $this->expectException(DeformComponentException::class);
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
        $this->expectException(DeformComponentException::class);
        ComponentFactory::build("NotAThing", "namespace","button");
    }

    public function testBuildWithBaseComponent()
    {
        $this->expectException(DeformComponentException::class);
        ComponentFactory::build(BaseComponent::class, "namespace","button");
    }

    public function testBuildWithWrongNamespace()
    {
        $this->expectException(DeformComponentException::class);
        ComponentFactory::build(FormModel::class, "namespace","button");
    }

    public function testBuildInvalidClass()
    {
        $this->expectException(DeformComponentException::class);
        ComponentFactory::build(ComponentControls::class, "namespace","button");
    }
}