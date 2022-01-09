<?php
namespace Deform\Html;

class HtmlTest extends \Codeception\Test\Unit
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

    public function testCallStaticBadTag()
    {
        $this->expectException(\Exception::class);
        Html::foo();
    }

    public function testCallStaticGoodTag()
    {
        $divTag = Html::div();
        $this->assertInstanceOf(HtmlTag::class, $divTag );
    }

    public function testIsSelfClosedTag()
    {
        $this->assertFalse(Html::isSelfClosedTag('div'));
        $this->assertTrue(Html::isSelfClosedTag('img'));
        $this->assertFalse(Html::isSelfClosedTag('foo'));
    }

    public function testIsStandardTag()
    {
        $this->assertTrue(Html::isStandardTag('div'));
        $this->assertFalse(Html::isStandardTag('img'));
        $this->assertFalse(Html::isStandardTag('foo'));
    }

    public function testIsRegisteredTag()
    {
        $this->assertTrue(Html::isRegisteredTag('div'));
        $this->assertTrue(Html::isRegisteredTag('img'));
        $this->assertFalse(Html::isRegisteredTag('foo'));
    }

}
