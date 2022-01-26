<?php
$formArray = [
    'tag' => 'form',
    'namespace' => 'login-form-example',
    'action' => '',
    'method' => 'post',
    'sections' => [
        [
            'class' => 'Deform\Component\Text',
            'name' => 'input',
            'properties' => [
                'datalist' => ['one','two','three']
            ]
        ],
        [
            'class' => 'Deform\Component\Password',
            'name' => 'login'
        ],
        [
            'html'=>'<div><div>wibble</div></div>'
        ],
        [
            'class' => 'Deform\Component\Submit',
            'name' => 'submit',
            'attributes' => [
                'value' => 'submit'
            ]
        ],
        [
            'class' => 'Deform\Component\Select',
            'name' => 'something',
            'properties' => [
                'hasOptGroups' => false,
                'options' => ['one','two','three'],
            ],
            'container' => [
                'hint' => 'whatevs'
            ]
        ]
    ]
];

echo "<pre>".htmlspecialchars(print_r($formArray,true))."</pre>";

$rebuildForm = \Deform\Form\Model\FormModel::buildForm($formArray);
echo $rebuildForm->getFormHtml();
