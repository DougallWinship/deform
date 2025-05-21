<?php
$exampleForm = new \App\ExampleFormModel();
$exampleForm->setCSRFStrategy(\Deform\Form\FormModel::CSRF_STRATEGY_COOKIE);
if ($exampleForm->isSubmitted()) {
    $formData = $exampleForm->getFormData();
    echo "<pre class='cookie'>".serialize($_COOKIE)."</pre>";
    echo "<pre>".print_r($formData, true)."</pre>".PHP_EOL;
}
$form = $exampleForm->run();
echo $form;