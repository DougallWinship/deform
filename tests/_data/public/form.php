<?php if (isset($_POST) && count($_POST)>0) { ?>
    <pre data-method="post"><?= serialize($_POST); ?></pre>
<?php } elseif (isset($_GET) && count($_GET)>0) { ?>
    <pre data-method="get"><?= serialize($_GET); ?></pre>
<?php } ?>

<?php
$exampleForm = new \Deform\Form\Model\ExampleFormModel();
$formHtml = $exampleForm->getFormHtml();
$formHtml->css('display','inline-block')
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

<pre><?= htmlspecialchars(print_r($exampleForm->toArray(),true)); ?></pre>