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

    public function testAddFailSelfClosing()
    {
        $this->expectException(\Exception::class);
        $br = Html::br();
        $br->add("you can't do this dummy");
    }

    public function testAddSingleString()
    {
        $div = Html::div();
        $div->add("foo");
        $html = (string)$div;
        $this->assertEquals("<div>foo</div>", $html);
    }

    public function testAddStringArray()
    {
        $div = Html::div();
        $div->add(["foo","bar"]);
        $html = (string)$div;
        $this->assertEquals("<div>foobar</div>", $html);
    }

    public function testAddSingleSelfClosingHtml()
    {
        $div = Html::div();
        $div->add(Html::hr());
        $html = (string)$div;
        $this->assertEquals("<div><hr></div>", $html);
    }

    public function testAddMultipleSelfClosingHtml()
    {
        $div = Html::div();
        $div->add([Html::br(),Html::br(),Html::hr()]);
        $html = (string)$div;
        $this->assertEquals("<div><br><br><hr></div>", $html);
    }

    public function testAddSingleNormalHtml()
    {
        $div = Html::div();
        $div->add(Html::span()->add('foo'));
        $html = (string)$div;
        $this->assertEquals("<div><span>foo</span></div>", $html);
    }

    public function testAddMultipleNormalHtml()
    {
        $div = Html::div();
        $div->add([Html::span()->add('foo'),Html::span()->add('bar')]);
        $html = (string)$div;
        $this->assertEquals("<div><span>foo</span><span>bar</span></div>", $html);
    }

    public function testAddMultipleNormalHtmlNested()
    {
        $div = Html::div();
        $div->add([Html::span()->add(Html::span()->add('foo')), Html::div()->add(Html::i()->add('bar'))]);
        $html = (string)$div;
        $this->assertEquals("<div><span><span>foo</span></span><div><i>bar</i></div></div>", $html);
    }

    public function testPrependStringFailSelfClosing()
    {
        $this->expectException(\Exception::class);
        $br = Html::br();
        $br->prepend("not allowed");
    }

    public function testPrependTagFailSelfClosing()
    {
        $this->expectException(\Exception::class);
        $br = Html::br();
        $br->prepend(Html::hr());
    }

    public function testPrependString()
    {
        $div = Html::div()->add('foo');
        $div->prepend('bar');
        $html = (string)$div;
        $this->assertEquals("<div>barfoo</div>", $html);
    }

    public function testPrependTag()
    {
        $div = Html::div()->add('foo');
        $div->prepend(Html::span()->add('bar'));
        $html = (string)$div;
        $this->assertEquals("<div><span>bar</span>foo</div>", $html);
    }

    public function testClearFailSelfClosing()
    {
        $this->expectException(\Exception::class);
        $br = Html::br();
        $br->clear();
    }

    public function testClearTag()
    {
        $div = Html::div()->add('foo')->add(Html::span()->add('bar'));
        $div->clear();
        $html=(string)$div;
        $this->assertEquals("<div></div>", $html);
    }

    public function testResetFailSelfClosing()
    {
        $this->expectException(\Exception::class);
        $br = Html::br();
        $br->reset("foo");
    }

    public function testResetString()
    {
        $div = Html::div()->add(Html::span()->add('foo'));
        $div->reset("bar");
        $html=(string)$div;
        $this->assertEquals("<div>bar</div>", $html);
    }

    public function testResetTag()
    {
        $div = Html::div()->add(Html::span()->add('foo'));
        $div->reset(Html::i()->add('bar'));
        $html=(string)$div;
        $this->assertEquals("<div><i>bar</i></div>", $html);
    }

    public function testIsSelfClosingFail()
    {
        $div = Html::div();
        $this->assertFalse($div->isSelfClosing());
    }

    public function testIsSelfClosingPass()
    {
        $br = Html::br();
        $this->assertTrue($br->isSelfClosing());
    }

    public function testGetChildrenEmpty()
    {
        $div = Html::div();
        $children = $div->getChildren();
        $this->assertEmpty($children);
    }

    public function testGetChildren()
    {
        $div = Html::div()
            ->add(Html::span()->add('foo'))
            ->add(Html::i()->add('bar'))
            ->add('string');
        $children = $div->getChildren();
        $this->assertCount(3, $children);
        $this->assertInstanceOf(HtmlTag::class, $children[0]);
        $this->assertEquals("<span>foo</span>", (string)$children[0]);
        $this->assertInstanceOf(HtmlTag::class, $children[1]);
        $this->assertEquals("<i>bar</i>", (string)$children[1]);
        $this->assertEquals("string", $children[2]);
    }

    public function testHasChildrenSelfClosingFalse()
    {
        $br = Html::br();
        $this->assertFalse($br->hasChildren());
    }

    public function testHasChildrenFalse()
    {
        $div = Html::div();
        $this->assertFalse($div->hasChildren());
    }

    public function testHasChildrenTrue()
    {
        $div = Html::div()->add('foo')->add('bar');
        $this->assertEquals(2, $div->hasChildren());
    }


}
