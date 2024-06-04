<?php
/**
 * @throws \Exception
 */
use \Deform\Html\Html as Html;

$html = Html::div(['id'=>'fromTag','class'=>'list-wrapper','style'=>'display:inline-block;padding:6px;border-radius:6px;background-color:#aaa;margin:10px;'])->add([
    Html::ul(['style'=>'padding-left:16px;margin:0'])->add([
        Html::li()->id('item1')->class('item')->add('item1'),
        Html::li()->id('item2')->class('item')->add('item2'),
        Html::li()->id('item3')->class('item')->add('item3'),
    ])
]);
$htmlDocument = \Deform\Html\HtmlDocument::load($html);
echo "<div>HtmlDocument::load(...) from an HtmlTag:</div>";
echo $htmlDocument."<br><br>";

$htmlDocument = \Deform\Html\HtmlDocument::load('<div id="fromString" class="list-wrapper" style="display:inline-block;padding:6px;border-radius:6px;background-color:#aaa;margin:10px;"><ul style="padding-left:16px;margin:0"><li id="item1" class="item">item1</li><li id="item2" class="item">item2</li><li id="item3" class="item">item3</li></ul></div>');
echo "<div>HtmlDocument::load(...) from a string:</div>";
echo $htmlDocument."<br><br>";

$htmlDocument->selectXPath('.//*[contains(concat(" ",normalize-space(@class)," ")," item ")][(count(preceding-sibling::*)+1) = 2]',function(\DOMNode $domNode) {
    $domNode->nodeValue = 'altered by xpath selector';
})->selectXPath("//li[contains(concat(' ', @class,' '),' item ')]", function(\DOMNode $domNode) {
    $domNode->setAttribute('style','color:green');
});
echo "<div>HtmlDocument altered by an xpath selector:</div>";
echo $htmlDocument . "<br><br>";

if ($htmlDocument->canConvertCssSelectorToXpath()) {
    $htmlDocument->selectCss('#fromString', function (\DOMNode $domNode) {
        $domNode->setAttribute('id', 'fromStringAlteredCss');
    })->selectCss('.item:nth-child(2)', function (\DOMNode $domNode) {
        $domNode->nodeValue = 'altered by css selector';
    })->selectCss('.item', function (\DOMNode $domNode) {
        $domNode->setAttribute('style', 'color:blue');
    });
    echo $htmlDocument . "<br><br>";
}
else {
    echo "<br><div>selectCss(...) is unavailable, if you wish to use css selectors them then please install "
        ."<a href='https://github.com/bkdotcom/CssXpath'>https://github.com/bkdotcom/CssXpath</a>"
        ." or use a css->xpath translation tool such as "
        ."<a href='https://css2xpath.github.io/'>https://css2xpath.github.io</a></div><br><br>";
}
