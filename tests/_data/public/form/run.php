<?php
$exampleForm = new \App\ExampleFormModel();
if ($exampleForm->isSubmitted()) {
    $formData = $exampleForm->getFormData();
    echo "<pre>".print_r($formData, true)."</pre>".PHP_EOL;
}
$exampleForm->setCSRFStrategy(\Deform\Form\FormModel::CSRF_STRATEGY_COOKIE);
$form = $exampleForm->run();
echo $form;