<?php
$exampleForm = new \App\ExampleFormModel();
if ($exampleForm->isSubmitted()) {
    $data = $exampleForm->getFormData();
    echo "<pre>".print_r($data, true)."</pre>";
}
?>

<?php
$exampleForm = new \App\ExampleFormModel();
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
    ->css('padding','14px')
    ->css('background-color','#ccd')
    ->css('margin','8px');
$formHtml->deform('.container-type-email',function($node) {
    $node->css('background-color','green')
        ->css('color','white')
        ->css('padding', '10px');
});
echo $formHtml;
?>

<br><br>

<h2>Rebuilt Form Model (from array)</h2>
<?php
$formArray['namespace'] = 'rebuilt-login-form';
$rebuildForm = \Deform\Form\FormModel::buildForm($formArray);
$formHtml = $rebuildForm->getFormHtml();
echo $formHtml;

?>

<br><br>
<h2>Document Deformed Form</h2>
<?php
$formArray['namespace'] = 'rebuilt-login-form-2';
$rebuildForm2 = \Deform\Form\FormModel::buildForm($formArray);
$htmlDocument = \Deform\Html\HtmlDocument::load($rebuildForm2->getFormHtml());
$htmlDocument->selectXPath(".//form",function(\DOMNode $domNode) {
    $domNode->setAttribute('id','loaded-deformed-form');
})->selectXPath('.//input',function(\DOMNode $domNode) {
    $domNode->setAttribute('style','background-color:green;color:white');
});
echo $htmlDocument;

