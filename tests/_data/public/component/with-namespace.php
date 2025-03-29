<?php
use \Deform\Component\ComponentFactory as Component;

if (isset($_POST) && count($_POST)) { ?>
<pre><?= serialize($_POST); ?></pre>
<?php } ?>

<h2>Components with Namespace 'form1'</h2>
<form method="post" enctype="multipart/form-data">
    <?= Component::Button('form1','mybutton')->value('buttonvalue')->html('Button') ?>

    <?= Component::Checkbox('form1','mycheckbox')->text('check me!')->label('Single Checkbox', true) ?>

    <?= Component::CheckboxMulti('form1','mymulticheckbox')->checkboxes(['true'=>'True','false'=>"False",'File Not Found'])->label('Multiple Checkboxes'); ?>

    <?= Component::ColorSelector('form1', 'mycolorselector')->label("Colour"); ?>

    <?= Component::Currency('form1', 'mycurrency')->currency('&pound;')->label('Currency'); ?>

    <?= Component::Date('form1','mydate')->label('Date'); ?>

    <?= Component::DateTime('form1','mydatetime')->label('Datetime'); ?>

    <?= Component::Display('form1','mydisplay')->value('something to show')->label('Display'); ?>

    <?= Component::Email( 'form1','myemail')->value('wibble@hatstand.org')->label('Email'); ?>

    <?= Component::File('form1', 'myfile')->label("File") ?>

    <?= Component::Hidden( 'form1','myhidden')->value('hidden value') ?>
    &laquo; hidden value!

    <?= Component::Image('form1','myimage')->label("Image") ?>

    <?= Component::MultipleEmail('form1','mymultiple-email')->label('Multiple Emails'); ?>

    <?= Component::MultipleFile('form1','mymultiple-file')->label('Multiple Files'); ?>

    <?= Component::Password('form1','mypassword')->label('Password') ?>

    <?= Component::RadioButtonSet( 'form1','myradiobuttonset')->radioButtons(['one'=>'One','two'=>'Two','three'=>'Three'])->label("Radio Button Set"); ?>

    <?= Component::RadioButtonSet('form1', 'myradiobuttonset2')->radioButtons(['three','four','five'])->setValue('four')->label("Radio Button Set 2"); ?>

    <?= Component::Select( 'form1','myselect')->options(['one'=>'One','two'=>'Two','three'=>'Three'])->label('Select')->setValue('two'); ?>

    <?= Component::Select( 'form1','myselect2')->optgroupOptions(['group 1'=>['one'=>'One','two'=>'Two','three'=>'Three'],'group 2'=>['four'=>'Four','five'=>'Five']])->label('Select with optgroups')->setValue('four') ?>

    <?= Component::SelectMulti('form1', 'myselectmulti')->options(['one'=>'One','two'=>'Two','three'=>'Three'])->label('Select Multi')->setValue(['two','three'])?>

    <?= Component::SelectMulti( 'form1','myselectmulti2')->optgroupOptions(['group 1'=>['one'=>'One','two'=>'Two','three'=>'Three'],'group 2'=>['four'=>'Four','five'=>'Five']])->label('Select Multi with optgroups')->setValue(['two','five']) ?>

    <?= Component::Slider('form1','myslider')->label('Slider') ?>

    <?= Component::Submit( 'form1','mysubmitbutton')->value('Submit Button') ?>

    <?= Component::Text('form1','mytext')->label('Text') ?>

    <?= Component::TextArea('form1', 'mytextarea')->label('Text Area') ?>

</form>

