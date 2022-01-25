<?php if (isset($_POST) && count($_POST)>0) { ?>
    <pre data-method="post"><?= serialize($_POST); ?></pre>
<?php } elseif (isset($_GET) && count($_GET)>0) { ?>
    <pre data-method="get"><?= serialize($_GET); ?></pre>
<?php } ?>

<h2>Form Model</h2>
<?php
$exampleForm = new \Deform\Form\Model\ExampleFormModel();
$formHtml = $exampleForm->getFormHtml();
echo $formHtml;
?>
<br>
<h3>toArray():</h3>
<pre>
<?php
$formArray = $exampleForm->toArray();
echo htmlspecialchars(print_r($formArray,true));
?>
</pre>

<br><br>

<h2>Deformed Form Model</h2>
<?php
$formHtml
    ->css('display','inline-block')
    ->css('border-radius','8px')
    ->css('padding','8px')
    ->css('background-color','#ccc')
    ->css('margin','8px');
$formHtml->deform('.container-type-email',function($node) {
    $node->css('background-color','green');
});
echo $formHtml;
?>

<br><br>

<h2>Rebuilt Form Model (from array)</h2>
<?php
$rebuildForm = \Deform\Form\Model\FormModel::buildForm($formArray);
$formHtml = $rebuildForm->getFormHtml();
echo $formHtml;
?>

<br><br>
<h2>Document Deformed Form</h2>
<?php
$htmlDocument = \Deform\Html\HtmlDocument::load($formHtml);
$htmlDocument->selectCss('input',function(\DOMNode $domNode) {
    $domNode->setAttribute('style','background-color:green');
});
echo $htmlDocument;