<?php
namespace Deform\Html;

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
    public function testButtonSetup()
    {
        //Q: should this be testing the structure of the generated button or just the process of construction?
        //A: the acceptance tests should check the structure of the html ... this should check the internal construction
        $button = ComponentFactory::Button('namespace','button', ['foo'=>'bar']);
        $autolabel = $this->tester->getAttributeValue($button, 'autoLabel');
        $this->assertFalse($autolabel);
        $buttonHtml  = $button->button;
        // not really bothered what's in the Html at this stage (that's for the acceptance tests)
        $this->assertInstanceOf(\Deform\Html\HtmlTag::class, $buttonHtml);

        $container = $this->tester->getAttributeValue($button, 'componentContainer');
        $this->assertInstanceOf(\Deform\Component\ComponentContainer::class, $container);
        $control = $this->tester->getAttributeValue($container, 'control');
        $this->assertInstanceOf(\Deform\Component\ComponentControls::class, $control);
    }

}
