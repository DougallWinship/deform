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
echo $htmlDocument."<br>";

$htmlDocument = \Deform\Html\HtmlDocument::load('<div id="fromString" class="list-wrapper" style="display:inline-block;padding:6px;border-radius:6px;background-color:#aaa;margin:10px;"><ul style="padding-left:16px;margin:0"><li id="item1" class="item">item1</li><li id="item2" class="item">item2</li><li id="item3" class="item">item3</li></ul></div>');
echo $htmlDocument."<br>";

$htmlDocument->selectXPath('.//*[contains(concat(" ",normalize-space(@class)," ")," item ")][(count(preceding-sibling::*)+1) = 2]',function(\DOMNode $domNode) {
    $domNode->nodeValue = 'altered by xpath selector';
})->selectXPath("//li[contains(concat(' ', @class,' '),' item ')]", function(\DOMNode $domNode) {
    $domNode->setAttribute('style','color:green');
});
echo $htmlDocument . "<br>";

if ($htmlDocument->canConvertCssSelectorToXpath()) {
    $htmlDocument->selectCss('#fromString', function (\DOMNode $domNode) {
        $domNode->setAttribute('id', 'fromStringAlteredCss');
    })->selectCss('.item:nth-child(2)', function (\DOMNode $domNode) {
        $domNode->nodeValue = 'altered by css selector';
    })->selectCss('.item', function (\DOMNode $domNode) {
        $domNode->setAttribute('style', 'color:blue');
    });
    echo $htmlDocument . "<br>";
}
