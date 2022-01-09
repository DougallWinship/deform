<?php
namespace Deform\Html;

class HtmlTagTest extends \Codeception\Test\Unit
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
    public function testConstructorFailsForBadTag()
    {
        $this->expectException(\Exception::class);
        new HtmlTag("foo");
    }

    public function testConstructorFailsForBadTag2()
    {
        $this->expectException(\Exception::class);
        new HtmlTag("foo",['bar']);
    }

    public function testConstructorPassesGoodTag()
    {
        $div = new HtmlTag("div");
        $this->assertInstanceOf(HtmlTag::class, $div);
    }

    public function testConstructorSetsTagName()
    {
        $div = new HtmlTag('div');
        $checkAttribute = $this->tester->getAttributeValue($div, 'tagName');
        $this->assertSame('div',$checkAttribute);
    }

    public function testConstructorSetsAttributes()
    {
        $tagAttributes = ['foo'=>'bar'];
        $div = new HtmlTag("div", $tagAttributes);
        $checkAttributes = $this->tester->getAttributeValue($div,'attributes');
        $this->assertSame($tagAttributes, $checkAttributes);
    }

    public function testConstructorSetsIsSelfClosing1()
    {
        $div = new HtmlTag("div");
        $checkIsSelfClosing = $this->tester->getAttributeValue($div,'isSelfClosing');
        $this->assertFalse($checkIsSelfClosing);
    }

    public function testConstructorSetsIsSelfClosing2()
    {
        $div = new HtmlTag("img");
        $checkIsSelfClosing = $this->tester->getAttributeValue($div,'isSelfClosing');
        $this->assertTrue($checkIsSelfClosing);
    }
}
