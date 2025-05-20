<?php
namespace App\Tests\Unit\Deform\Component;

use Deform\Component\ComponentFactory;
use Deform\Exception\DeformComponentException;

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

        $this->expectException(DeformComponentException::class);
        $hidden->getHtmlTag();
    }

    public function testControlOnlyAttributes()
    {
        $hidden = ComponentFactory::Hidden('ns','hd', ['foo'=>'bar']);
        $hiddenTag = $hidden->getHtmlTag();
        $this->tester->assertIsHtmlTag($hiddenTag,'input',['foo'=>'bar']);
    }
}