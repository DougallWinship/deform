<?php
$htmlString = <<<PHP
    <div style='background-color: #ccc; border-radius:10px;display:inline-block; padding:10px;'>
        <h1>Sample HTML Snippet</h1>
        <ul>
            <li style="color:red">one</li>
            <li style="color:green">two</li>
            <li style="color:blue">three</li>
        </ul>
    </div>
PHP;
echo "<div class='source'>".$htmlString."</div>";

$HtmlDocument = \Deform\Html\HtmlDocument::load($htmlString);
echo "<br><br>This is rebuilt from HtmlDocument:<br><br>";
echo "<div class='html-document'>".$HtmlDocument."</div>";

$htmlTag = $HtmlDocument->getHtmlRootTag();
echo "<br><br>This is rebuilt from HtmlTag:<br><br>";
echo "<div class='html-tag'>".$htmlTag."</div>";