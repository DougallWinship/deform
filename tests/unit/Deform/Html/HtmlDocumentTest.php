<?php
namespace Deform\Html;

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

    public function testGetHtmlRootTag()
    {
        $htmlDocument = HtmlDocument::load("<div id='maindiv'><ul><li>one</li><li>two</li><li>three</li></ul></div>");
        $htmlRootTag = $htmlDocument->getHtmlRootTag();
        $this->assertInstanceOf(HtmlTag::class, $htmlRootTag);
        $html = (string)$htmlRootTag;
        $this->assertEquals("<div id='maindiv'><ul><li>one</li><li>two</li><li>three</li></ul></div>", $html);
    }

    public function testSelectCss()
    {
        $htmlDocument = HtmlDocument::load("<div><ul><li class='noinput'>one</li><li class='noinput'>two</li><li><input name='name' value='three' /></li></ul></div>");
        if ($htmlDocument->canConvertCssSelectorToXpath()) {
            $htmlDocument->selectCss('input[name=name][value=three]', function(\DOMNode $node) {
                $node->setAttribute('value','foo');
            });
            $htmlDocument->selectCss('.noinput', function(\DOMNode $node) {
                $node->setAttribute('class', 'bar');
            });
            $html = (string)$htmlDocument;
            $this->assertEquals('<div><ul><li class="bar">one</li><li class="bar">two</li><li><input name="name" value="foo"></li></ul></div>', $html);
        }
        else {
            $this->markTestSkipped('Automatic Css to XPath conversion is unavailable. Install https://github.com/bkdotcom/CssXpath to enable it.');
        }
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

    public function testCanConvertCssSelectorToXPath()
    {
        $htmlDocument = HtmlDocument::load("<div></div>");
        $isInstalled = \Composer\InstalledVersions::isInstalled('bdk/css-xpath');
        $canConvert = $htmlDocument->canConvertCssSelectorToXpath();
        $this->assertEquals($isInstalled, $canConvert);
    }
}
