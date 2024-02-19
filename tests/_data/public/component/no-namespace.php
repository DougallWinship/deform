<?php
use \Deform\Component\ComponentFactory as Component;

if (isset($_POST) && count($_POST)) { ?>
    <pre><?= serialize($_POST); ?></pre>
<?php } ?>

<h2>Components without Namespace</h2>
<form method="post" enctype="multipart/form-data">
    <?= Component::Button(null,'mybutton')->value('buttonvalue')->html('press me!') ?>

    <?= Component::Checkbox(null,'mycheckbox')->checked('checked')->text('check me!')->label('Check it out!') ?>

    <?= Component::CheckboxMulti(null,'mymulticheckbox')->checkboxes(['true'=>'True','false'=>"False",'File Not Found'])->label('New Booleans'); ?>

    <?= Component::ColorSelector(null, 'mycolorselector')->label("select a colour!"); ?>

    <?= Component::Currency(null, 'mycurrency')->currency('&pound;')->label('Pay this:'); ?>

    <?= Component::Date(null,'mydate')->label('My Date'); ?>

    <?= Component::DateTime(null,'mydatetime')->label('My Datetime'); ?>

    <?= Component::Display(null,'mydisplay')->value('something to show')->label('My Display'); ?>

    <?= Component::Email( null,'myemail')->value('wibble@hatstand.org')->label('My Email'); ?>

    <?= Component::File(null, 'myfile')->label("My File"); ?>

    <?= Component::Hidden( null,'myhidden')->value('hidden value'); ?>

    <?= Component::Image(null, 'myimage')->label("My Image") ?>

    <?= Component::MultipleEmail(null,'mymultiple-email')->label('My Multiple Email'); ?>

    <?= Component::MultipleFile(null,'mymultiple-file')->label('My Multiple File'); ?>

    <?= Component::Password(null,'mypassword')->label('My Password'); ?>

    <?= Component::RadioButtonSet( null,'myradiobuttonset')->radioButtons(['one'=>'One','two'=>'Two','three'=>'Three'])->label("My Radio Button Set"); ?>

    <?= Component::RadioButtonSet(null, 'myradiobuttonset2')->radioButtons(['three','four','five'])->setValue('four')->label("My Radio Button Set 2"); ?>

    <?= Component::Select( null,'myselect')->options(['one'=>'One','two'=>'Two','three'=>'Three'])->label('My Select')->setValue('two'); ?>

    <?= Component::Select( null,'myselect2')->optgroupOptions(['group 1'=>['one'=>'One','two'=>'Two','three'=>'Three'],'group 2'=>['four'=>'Four','five'=>'Five']])->label('My Select (optgroups)')->setValue('four') ?>

    <?= Component::Slider(null,'myslider')->label('My Slider'); ?>

    <?= Component::SelectMulti(null, 'myselectmulti')->options(['one'=>'One','two'=>'Two','three'=>'Three'])->label('My Select Multi')->setValue(['two','three'])?>

    <?= Component::SelectMulti( null,'myselectmulti2')->optgroupOptions(['group 1'=>['one'=>'One','two'=>'Two','three'=>'Three'],'group 2'=>['four'=>'Four','five'=>'Five']])->label('My Select Multi (optgroups)')->setValue(['two','five']) ?>

    <?= Component::Submit( null,'mysubmitbutton')->value('My Submit Button'); ?>

    <?= Component::Text(null,'mytext')->label('My Text'); ?>

    <?= Component::TextArea(null, 'mytextarea')->label('My Text Area'); ?>

</form>
