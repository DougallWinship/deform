<?php
use \Deform\Html\Html as Html;

$html = Html::div()->css('border','10px solid red')->class('outerdiv')->onclick("alert('div')")->add(
    Html::hr()->css('border','10px solid green')->class('innerhr')->onclick("alert('hr')")
);

echo $html."<br>";

$html->clear()->add(Html::span()->css('color','blue')->class('blue-text')->add('Blue Text'))."<br>";

echo $html."<br>";

$html->deform('.blue-text',function(\Deform\Html\HtmlTag $node) {
    $node->clear()->css('color','green')->clear()->add('Green Text');
})."<br>";

echo $html."<br>";
?>
