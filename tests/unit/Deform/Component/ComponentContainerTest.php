<?php
namespace Deform\Component;

use Deform\Html\Html;

class ComponentContainerTest extends \Codeception\Test\Unit
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

    public function testControlOnlyException()
    {
        $hidden = ComponentFactory::Hidden('ns','hd');
        $container = $this->tester->getAttributeValue($hidden, 'componentContainer');
        $control = $this->tester->getAttributeValue($container, 'control');
        $this->tester->setAttributeValue($control,'controlTags',[1,2,3,4]);

        $this->expectException(\Exception::class);
        $hidden->getHtmlTag();
    }

    public function testControlOnlyAttributes()
    {
        $hidden = ComponentFactory::Hidden('ns','hd', ['foo'=>'bar']);
        $hiddenTag = $hidden->getHtmlTag();
        $this->tester->assertIsHtmlTag($hiddenTag,'input',['foo'=>'bar']);
    }
}