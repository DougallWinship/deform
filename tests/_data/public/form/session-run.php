<?php
session_start();
$exampleForm = new \App\ExampleFormModel();
if ($exampleForm->isSubmitted()) {
    $formData = $exampleForm->getFormData();
    echo "<pre class='session'>".serialize($_SESSION)."</pre>";
    echo "<pre>".print_r($formData, true)."</pre>".PHP_EOL;
}
$form = $exampleForm->run();
echo $form;