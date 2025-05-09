<?php
use \Deform\Component\ComponentFactory as Component;

if (isset($_POST) && count($_POST)) { ?>
    <pre><?= serialize($_POST); ?></pre>
<?php } ?>

<h2>Components without Namespace</h2>
<form method="post" enctype="multipart/form-data">
    <?= Component::Button(null,'mybutton')->value('buttonvalue')->html('Button') ?>

    <?= Component::Checkbox(null,'mycheckbox')->checked('checked')->text('check me!')->label('Single Checkbox') ?>

    <?= Component::CheckboxMulti(null,'mymulticheckbox')->checkboxes(['true'=>'True','false'=>"False",'File Not Found'])->label('Multiple Checkboxes'); ?>

    <?= Component::ColorSelector(null, 'mycolorselector')->label("Colour Selector")->value("#aaffaa") ?>

    <?= Component::Currency(null, 'mycurrency')->currency('&pound;')->label('Currency'); ?>

    <?= Component::Date(null,'mydate')->label('Date'); ?>

    <?= Component::DateTime(null,'mydatetime')->label('Datetime'); ?>

    <?= Component::Decimal(null, 'mydecimal')->label('Decimal')->dp(2); ?>

    <?= Component::Display(null,'mydisplay')->value('something to show')->label('Display'); ?>

    <?= Component::Email( null,'myemail')->value('wibble@hatstand.org')->label('Email'); ?>

    <?= Component::File(null, 'myfile')->label("File"); ?>

    <?= Component::Hidden( null,'myhidden')->value('hidden value'); ?>
    &laquo; hidden value!

    <?= Component::Image(null, 'myimage')->label("Image") ?>

    <?= Component::Integer(null, 'myinteger')->value("1") ?>

    <?= Component::MultipleEmail(null,'mymultiple-email')->label('Multiple Email'); ?>

    <?= Component::MultipleFile(null,'mymultiple-file')->label('Multiple File'); ?>

    <?= Component::Password(null,'mypassword')->label('Password'); ?>

    <?= Component::RadioButtonSet( null,'myradiobuttonset')->radioButtons(['one'=>'One','two'=>'Two','three'=>'Three'])->label("Radio Button Set"); ?>

    <?= Component::RadioButtonSet(null, 'myradiobuttonset2')->radioButtons(['three','four','five'])->setValue('four')->label("Radio Button Set 2"); ?>

    <?= Component::Select( null,'myselect')->options(['one'=>'One','two'=>'Two','three'=>'Three'])->label('Select')->setValue('two'); ?>

    <?= Component::Select( null,'myselect2')->optgroupOptions(['group 1'=>['one'=>'One','two'=>'Two','three'=>'Three'],'group 2'=>['four'=>'Four','five'=>'Five']])->label('Select with optgroups')->setValue('four') ?>

    <?= Component::Slider(null,'myslider')->label('Slider')->showOutput() ?>

    <?= Component::SelectMulti(null, 'myselectmulti')->options(['one'=>'One','two'=>'Two','three'=>'Three'])->label('Select Multi')->setValue(['two','three'])?>

    <?= Component::SelectMulti( null,'myselectmulti2')->optgroupOptions(['group 1'=>['one'=>'One','two'=>'Two','three'=>'Three'],'group 2'=>['four'=>'Four','five'=>'Five']])->label('Select Multi with optgroups')->setValue(['two','five']) ?>

    <?= Component::Submit( null,'mysubmitbutton')->value('Submit Button'); ?>

    <?= Component::Text(null,'mytext')->label('Text')->value("text"); ?>

    <?= Component::TextArea(null, 'mytextarea')->label('Text Area'); ?>

</form>
