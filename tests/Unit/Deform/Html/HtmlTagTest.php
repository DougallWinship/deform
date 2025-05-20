<?php
namespace App\Tests\Unit\Deform\Html;

use Deform\Exception\DeformHtmlException;
use Deform\Html\Html;
use Deform\Html\HtmlTag;

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
        $this->expectException(DeformHtmlException::class);
        new HtmlTag("foo");
    }

    public function testConstructorFailsForBadTag2()
    {
        $this->expectException(DeformHtmlException::class);
        new HtmlTag("foo", ['bar']);
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
        $this->assertSame('div', $checkAttribute);
    }

    public function testConstructorSetsAttributes()
    {
        $tagAttributes = ['foo' => 'bar'];
        $div = new HtmlTag("div", $tagAttributes);
        $checkAttributes = $this->tester->getAttributeValue($div, 'attributes');
        $this->assertSame($tagAttributes, $checkAttributes);
    }

    public function testConstructorSetsIsSelfClosing1()
    {
        $div = new HtmlTag("div");
        $checkIsSelfClosing = $this->tester->getAttributeValue($div, 'isSelfClosing');
        $this->assertFalse($checkIsSelfClosing);
    }

    public function testConstructorSetsIsSelfClosing2()
    {
        $div = new HtmlTag("img");
        $checkIsSelfClosing = $this->tester->getAttributeValue($div, 'isSelfClosing');
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
        $div->add(["foo", "bar"]);
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
        $div->add([Html::br(), Html::br(), Html::hr()]);
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
        $div->add([Html::span()->add('foo'), Html::span()->add('bar')]);
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
        $this->expectException(DeformHtmlException::class);
        $br = Html::br();
        $br->prepend("not allowed");
    }

    public function testPrependTagFailSelfClosing()
    {
        $this->expectException(DeformHtmlException::class);
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
        $this->expectException(DeformHtmlException::class);
        $br = Html::br();
        $br->clear();
    }

    public function testClearTag()
    {
        $div = Html::div()->add('foo')->add(Html::span()->add('bar'));
        $div->clear();
        $html = (string)$div;
        $this->assertEquals("<div></div>", $html);
    }

    public function testResetFailSelfClosing()
    {
        $this->expectException(DeformHtmlException::class);
        $br = Html::br();
        $br->reset("foo");
    }

    public function testResetString()
    {
        $div = Html::div()->add(Html::span()->add('foo'));
        $div->reset("bar");
        $html = (string)$div;
        $this->assertEquals("<div>bar</div>", $html);
    }

    public function testResetTag()
    {
        $div = Html::div()->add(Html::span()->add('foo'));
        $div->reset(Html::i()->add('bar'));
        $html = (string)$div;
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

    public function testSetViaConstructor()
    {
        $div = Html::div(['foo' => 'bar']);
        $attributes = $this->tester->getAttributeValue($div, 'attributes');
        $this->assertEquals(['foo' => 'bar'], $attributes);
    }

    public function testSetAttribute()
    {
        $div = Html::div();
        $div->set('foo', 'bar');
        $attributes = $this->tester->getAttributeValue($div, 'attributes');
        $this->assertEquals(['foo' => 'bar'], $attributes);
    }

    public function testSetIfExistsFail()
    {
        $div = Html::div();
        $div->setIfExists('foo', 'bar');
        $attributes = $this->tester->getAttributeValue($div, 'attributes');
        $this->assertEquals([], $attributes);
    }

    public function testSetIfExistsTrue()
    {
        $div = Html::div(['foo' => 'bar']);
        $div->setIfExists('foo', 'baz');
        $attributes = $this->tester->getAttributeValue($div, 'attributes');
        $this->assertEquals(['foo' => 'baz'], $attributes);
    }


    public function testSetManyWithoutReplacements()
    {
        $div = Html::div(['foo' => 'fooval', 'bar' => 'barval']);
        $div->setMany(['foo2' => 'foo2val', 'bar2' => 'bar2val']);
        $attributes = $this->tester->getAttributeValue($div, 'attributes');
        $this->assertEquals(['foo' => 'fooval', 'bar' => 'barval', 'foo2' => 'foo2val', 'bar2' => 'bar2val'], $attributes);
    }

    public function testSetManyWithReplacements()
    {
        $div = Html::div(['foo' => 'fooval', 'bar' => 'barval']);
        $div->setMany(['foo' => 'baz', 'bar2' => 'bar2val']);
        $attributes = $this->tester->getAttributeValue($div, 'attributes');
        $this->assertEquals(['foo' => 'baz', 'bar' => 'barval', 'bar2' => 'bar2val'], $attributes);
    }

    public function testSetIfEmptyTrue()
    {
        $div = Html::div(['foo' => 'foovar', 'bar' => 'barval']);
        $div->setIfEmpty('baz', 'bazval');
        $attributes = $this->tester->getAttributeValue($div, 'attributes');
        $this->assertEquals(['foo' => 'foovar', 'bar' => 'barval', 'baz' => 'bazval'], $attributes);
    }

    public function testSetIfEmptyFalse()
    {
        $div = Html::div(['foo' => 'foovar', 'bar' => 'barval']);
        $div->setIfEmpty('bar', 'bazval');
        $attributes = $this->tester->getAttributeValue($div, 'attributes');
        $this->assertEquals(['foo' => 'foovar', 'bar' => 'barval'], $attributes);
    }


    public function testSetUnsetDoesntExist()
    {
        $div = Html::div(['foo' => 'fooval', 'bar' => 'barval']);
        $div->unset('baz');
        $attributes = $this->tester->getAttributeValue($div, 'attributes');
        $this->assertEquals(['foo' => 'fooval', 'bar' => 'barval'], $attributes);
    }

    public function testSetUnsetExists()
    {
        $div = Html::div(['foo' => 'fooval', 'bar' => 'barval']);
        $div->unset('bar');
        $attributes = $this->tester->getAttributeValue($div, 'attributes');
        $this->assertEquals(['foo' => 'fooval'], $attributes);
    }

    public function testHasTrue()
    {
        $div = Html::div(['foo' => 'bar']);
        $this->assertTrue($div->has('foo'));
    }

    public function testHasFalse()
    {
        $div = Html::div(['foo' => 'bar']);
        $this->assertFalse($div->has('baz'));
    }

    public function testGetExists()
    {
        $div = Html::div(['foo' => 'bar']);
        $this->assertEquals('bar', $div->get('foo'));
    }

    public function testGetNonExists()
    {
        $div = Html::div(['foo' => 'bar']);
        $this->assertNull($div->get('baz'));
    }

    public function testMagicSetter()
    {
        $div = Html::div()->foo('fooval')->bar('barval');
        $attributes = $this->tester->getAttributeValue($div, 'attributes');
        $this->assertEquals(['foo' => 'fooval', 'bar' => 'barval'], $attributes);
    }

    public function testGetTagType()
    {
        $div = Html::div();
        $this->assertEquals('div', $div->getTagType());
    }

    public function testCss()
    {
        $div = Html::div(["style" => "color:red;background-color:green;position:relative;top:-10px;padding:10px;margin:8px"]);
        $div->css("background-color", "purple");
        $div->css("top", "0");
        $div->css("color", "green");
        $div->css("margin", "4px");
        $div->css("border", "1px solid red");
        $attributes = $this->tester->getAttributeValue($div, 'attributes');
        $style = $attributes['style'];
        $styleParts = explode(";", $style);
        $this->assertContains('background-color:purple', $styleParts);
        $this->assertContains('top:0', $styleParts);
        $this->assertContains('color:green', $styleParts);
        $this->assertContains('margin:4px', $styleParts);
        $this->assertContains('border:1px solid red', $styleParts);
        $this->assertCount(7, $styleParts);
    }

    public function testImplodeAttributeValuesStyle()
    {
        $imploded = HtmlTag::implodeAttributeValues('style', ['color:red', 'background-color:green', 'position:relative', 'top:4px']);
        $this->assertEquals('color:red;background-color:green;position:relative;top:4px', $imploded);
    }

    public function testImplodeAttributeValuesClass()
    {
        $imploded = HtmlTag::implodeAttributeValues('class', ['foo', 'bar', 'baz']);
        $this->assertEquals('foo bar baz', $imploded);
    }

    public function testImplodeAttributeValuesOn()
    {
        $imploded = HtmlTag::implodeAttributeValues('onclick', ["alert('foo')", "alert('bar')", "alert('baz')"]);
        $this->assertEquals("alert('foo');alert('bar');alert('baz')", $imploded);

        // it's up to the code user to provide a valid on****!
        $imploded = HtmlTag::implodeAttributeValues('onfoo', ["alert('foo')", "alert('bar')", "alert('baz')"]);
        $this->assertEquals("alert('foo');alert('bar');alert('baz')", $imploded);
    }

    public function testImplodeAttributeValuesOther()
    {
        $imploded = HtmlTag::implodeAttributeValues('other', ["foo", "bar", "baz"]);
        $this->assertEquals("baz", $imploded);
    }

    public function testImplodeAttributeValuesNonString()
    {
        $this->expectException(DeformHtmlException::class);
        HtmlTag::implodeAttributeValues('other', ["foo", "bar", new \stdClass()]);
    }

    public function testAttributesString()
    {
        $attrString = HtmlTag::attributesString([
            1 => 'discard this',
            'discard this too' => new \stdClass(),
            'tooltip' => "<'blah'>&\"",
            'style' => ['color:red', 'background-color:green', 'position:relative', 'top:4px'],
            'class' => ['foo', 'bar', 'baz'],
            'onclick' => ["alert('foo')", "alert('bar')", "alert('baz')"],
            'selected' => true,
            // in HTML if checked/selected is present (with or without a value) it should be honoured, however we're
            // still in PHP land at this stage:
            'checked' => false,
            'other' => ["foo", "bar", "baz"],
        ]);
        //$this->assertEquals(" tooltip='&lt;'blah'&gt;&amp;&quot;' style='color:red;background-color:green;position:relative;top:4px' class='foo bar baz' onclick='alert('foo');alert('bar');alert('baz')' selected other='baz'", $attrString);
        $this->assertEquals(" tooltip='&lt;&#039;blah&#039;&gt;&amp;&quot;' style='color:red;background-color:green;position:relative;top:4px' class='foo bar baz' onclick='alert(&#039;foo&#039;);alert(&#039;bar&#039;);alert(&#039;baz&#039;)' selected other='baz'", $attrString);
    }

    public function testDeform1()
    {
        $div = Html::div()->id('foo')->add(Html::ul()->class('bar')->add([
            Html::li()->class('items')->value('item1'),
            Html::li()->id('item2')->class('items')->value('item2'),
            Html::li()->class('items')->value('item3'),
            Html::li()->id('not-an-item')->value('not an item')
        ]));

        // deform by tag
        $div->deform('li', function (HtmlTag $htmlTag) {
            $htmlTag->class('liclass');
        });
        $html = (string)$div;
        $this->assertEquals("<div id='foo'><ul class='bar'><li class='liclass' value='item1'></li><li id='item2' class='liclass' value='item2'></li><li class='liclass' value='item3'></li><li id='not-an-item' value='not an item' class='liclass'></li></ul></div>", $html);

        // deform by id
        $div->deform('#not-an-item', function (HtmlTag $htmlTag) {
            $htmlTag->value("item4");
            $htmlTag->unset('id');
        });
        $html = (string)$div;
        $this->assertEquals("<div id='foo'><ul class='bar'><li class='liclass' value='item1'></li><li id='item2' class='liclass' value='item2'></li><li class='liclass' value='item3'></li><li value='item4' class='liclass'></li></ul></div>", $html);

        // deform by class
        $div->deform('.liclass', function (HtmlTag $htmlTag) {
            $htmlTag->class('items');
        });
        $html = (string)$div;
        $this->assertEquals("<div id='foo'><ul class='bar'><li class='items' value='item1'></li><li id='item2' class='items' value='item2'></li><li class='items' value='item3'></li><li value='item4' class='items'></li></ul></div>", $html);
    }

    public function testGetDomNode()
    {
        $domDocument = new \DOMDocument();
        $div = Html::div()->id('foo')->add(Html::ul()->class('bar')->add([
            Html::li()->class('items')->value('item1'),
            Html::li()->class('items')->value('item2'),
            Html::li()->class('items')->value('item3'),
        ]))->add("some text");
        $domNode = $div->getDomNode($domDocument);
        $this->assertInstanceOf(\DOMNode::class, $domNode);
        $html = $domDocument->saveHTML($domNode);
        $this->assertEquals('<div id="foo"><ul class="bar"><li class="items" value="item1"><li class="items" value="item2"><li class="items" value="item3"></ul>some text</div>', $html);
    }
}
