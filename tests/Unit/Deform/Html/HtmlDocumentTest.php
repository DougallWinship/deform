<?php
namespace Deform\Html;

use Deform\Exception\DeformHtmlException;

class HtmlDocumentTest extends \Codeception\Test\Unit
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

    public function testLoadBad()
    {
        $htmlDocument = HtmlDocument::load(">><<<>aaaagh!>><><<>>");
        $this->assertTrue($htmlDocument->hasErrors());
    }

    public function testLoadHtml5Tag()
    {
        $html = <<<HTML
<div id='rebuilt-login-form-txdl-container' class='component-container container-type-text'>
    <div class='label-container'>
        <label for="text-rebuilt-login-form-txdl" style='margin-bottom:0'>text datalist</label>
    </div>
    <div class='control-container'>
        <input id='text-rebuilt-login-form-txdl' name='rebuilt-login-form[txdl]' type='text' list='example[txdl]-datalist' onmouseover='focus()'>
        <datalist id='example[txdl]-datalist'><option value='one'></option><option value='two'></option><option value='three'></option><option value='four'></option><option value='five'></option><option value='six'></option></datalist>
    </div>
</div>
HTML;
        $htmlDocument = HtmlDocument::load($html);
        $this->assertFalse($htmlDocument->hasErrors());
    }

    public function testLoad()
    {
        $htmlDocument = HtmlDocument::load("<div><ul><li>one</li><li>two</li><li>three</li></ul></div>");
        $domDocument = $this->tester->getAttributeValue($htmlDocument,'domDocument');
        $this->assertInstanceOf(\DOMDocument::class,$domDocument);
        $html = $domDocument->saveHTML();
        $this->assertEquals("<div><ul><li>one</li><li>two</li><li>three</li></ul></div>\n",$html);
    }

    public function testLoadStringable()
    {
        $divTag = Html::div()->add(Html::ul()->add(Html::li()->add('one'))->add(Html::li()->add('two'))->add(Html::li()->add('three')));
        $htmlDocument = HtmlDocument::load($divTag);
        $domDocument = $this->tester->getAttributeValue($htmlDocument,'domDocument');
        $this->assertInstanceOf(\DOMDocument::class,$domDocument);
        $html = $domDocument->saveHTML();
        $this->assertEquals("<div><ul><li>one</li><li>two</li><li>three</li></ul></div>\n",$html);
    }

    public function testLoadInvalidTagFails()
    {
        $htmlDocument = HtmlDocument::load("<div><invalid-tag /></div>");
        $this->assertTrue($htmlDocument->hasErrors());
        $errors = $htmlDocument->getErrors();
        $this->assertCount(1, $errors);
        $error = $errors[0];
        $this->assertInstanceOf(\LibXMLError::class, $error);
        $this->assertEquals("Tag invalid-tag invalid\n", $error->message);
    }

    public function testLoadAddAllowInvalidTag()
    {
        HtmlDocument::addAllowedTags(["invalid-tag"]);
        $htmlDocument = HtmlDocument::load("<div><invalid-tag /></div>");
        $this->assertFalse($htmlDocument->hasErrors());
        $html = (string)$htmlDocument;
        $this->assertEquals("<div><invalid-tag></invalid-tag></div>", $html);
    }

    public function testLoadSetAllowInvalidTagFail()
    {
        HtmlDocument::setAllowedTags(["only-invalid-tag"]);
        $htmlDocument = HtmlDocument::load("<div><other-invalid-tag /></div>");
        $this->assertTrue($htmlDocument->hasErrors());
        $errors = $htmlDocument->getErrors();
        $this->assertCount(1, $htmlDocument->getErrors());
        $error = $errors[0];
        $this->assertInstanceOf(\LibXMLError::class, $error);
        $this->assertEquals("Tag other-invalid-tag invalid\n", $error->message);
    }

    public function testLoadSetAllowInvalidTag()
    {
        HtmlDocument::setAllowedTags(["invalid-tag"]);
        $htmlDocument = HtmlDocument::load("<div><invalid-tag /></div>");
        $this->assertFalse($htmlDocument->hasErrors());
        $html = (string)$htmlDocument;
        $this->assertEquals("<div><invalid-tag></invalid-tag></div>", $html);
    }

    public function testGetAllowedTagsDefault() {
        HtmlDocument::resetAllowedTags();
        HtmlDocument::addAllowedTags(["invalid-tag1","invalid-tag2"]);
        $expected = [
            'article', 'aside', 'details', 'figcaption', 'figure',
            'footer', 'header', 'main', 'mark', 'nav', 'section', 'summary',
            'time', 'datalist', 'canvas', 'svg', 'video', 'audio',
            'invalid-tag1', 'invalid-tag2'
        ];
        $allowed = HtmlDocument::getAllowedTags();
        $this->assertEquals($expected, $allowed);
    }

    public function testGetAllowedTags() {
        HtmlDocument::setAllowedTags(["invalid-tag1"]);
        HtmlDocument::addAllowedTags(["invalid-tag2"]);
        $allowedTags = HtmlDocument::getAllowedTags();
        $this->assertCount(2, $allowedTags);
        $this->assertContains("invalid-tag1", $allowedTags);
        $this->assertContains("invalid-tag2", $allowedTags);
    }

    public function testLoadAllowAll()
    {
        $htmlDocument = HtmlDocument::load("<div><invalid-tag /></div>", true);
        $html = (string)$htmlDocument;
        $this->assertEquals("<div><invalid-tag></invalid-tag></div>", $html);
    }

    public function testGetHtmlRootTag()
    {
        $htmlDocument = HtmlDocument::load("<div id='maindiv'><ul><li>one</li><li>two</li><li>three</li></ul></div>");
        $htmlRootTag = $htmlDocument->getHtmlRootTag();
        $this->assertInstanceOf(HtmlTag::class, $htmlRootTag);
        $html = (string)$htmlRootTag;
        $this->assertEquals("<div id='maindiv'><ul><li>one</li><li>two</li><li>three</li></ul></div>", $html);
    }

    public function testSelectXpath()
    {
        $htmlDocument = HtmlDocument::load("<div><ul><li class='noinput'>one</li><li class='noinput'>two</li><li><input name='name' value='three' /></li></ul></div>");
        $htmlDocument->selectXPath('.//input[@name="name"][@value="three"]', function(\DOMNode $node) {
            $node->setAttribute('value','foo');
        });
        $htmlDocument->selectXPath('.//*[contains(concat(" ",normalize-space(@class)," ")," noinput ")]', function(\DOMNode $node) {
            $node->setAttribute('class', 'bar');
        });
        $html = (string)$htmlDocument;
        $this->assertEquals('<div><ul><li class="bar">one</li><li class="bar">two</li><li><input name="name" value="foo"></li></ul></div>', $html);
    }

    public function testSelectCssNoConverter()
    {
        $this->assertFalse(HtmlDocument::canSelectCss());
        $this->expectException(DeformHtmlException::class);
        $this->expectExceptionMessage("If you want to use css selectors then please specify a converter via setCssToXpathConverter");

        $htmlDocument = HtmlDocument::load("<div><ul><li class='noinput'>one</li><li class='noinput'>two</li><li><input name='name' value='three' /></li></ul></div>");
        $htmlDocument->selectCss('input[name=name][value=three]', function(\DOMNode $node) {
            $node->setAttribute('value','foo');
        });
    }

    public function testSelectCss()
    {
        $this->assertFalse(HtmlDocument::canSelectCss());

        $htmlDocument = HtmlDocument::load("<div><ul><li class='noinput'>one</li><li class='noinput'>two</li><li><input name='name' value='three' /></li></ul></div>");

        // very limited converter just to support the specific test!
        HtmlDocument::setCssToXpathConverter(function($css) {
            if ($css==='input[name=name][value=three]') {
                return './/input[@name="name"][@value="three"]';
            }
            else if ($css==='.noinput') {
                return './/*[contains(concat(" ",normalize-space(@class)," ")," noinput ")]';
            }
            else {
                throw new \Exception("Unsupported selector: $css");
            }
        });

        $this->assertTrue(HtmlDocument::canSelectCss());

        $htmlDocument->selectCss('input[name=name][value=three]', function(\DOMNode $node) {
            $node->setAttribute('value','foo');
        });
        $htmlDocument->selectCss('.noinput', function(\DOMNode $node) {
            $node->setAttribute('class', 'bar');
        });
        $html = (string)$htmlDocument;
        $this->assertEquals('<div><ul><li class="bar">one</li><li class="bar">two</li><li><input name="name" value="foo"></li></ul></div>', $html);
    }
}
