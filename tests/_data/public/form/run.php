<?php
$exampleForm = new \App\ExampleFormModel();
if ($exampleForm->isSubmitted()) {
    $formData = $exampleForm->getFormData();
    echo "<pre>".serialize($_POST)."</pre>".PHP_EOL;
}
$exampleForm->setCSRFStrategy(\Deform\Form\FormModel::CSRF_STRATEGY_COOKIE);
$form = $exampleForm->run();
echo $form;