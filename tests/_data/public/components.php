<?php
use \Deform\Component\ComponentFactory as Component;

if (isset($_POST) && count($_POST)) { ?>
<pre><?= serialize($_POST); ?></pre>
<?php } ?>

<form method="post">
    <?= Component::Button('form1','mybutton')->value('buttonvalue')->text('press me!') ?>

    <?= Component::Checkbox('form1', 'mycheckbox')->text('check me!')->label('Check it out!') ?>

    <?= Component::CheckboxMulti('form1', 'mymulticheckbox')->checkboxes(['true'=>'True','false'=>"False",'File Not Found'])->label('New Booleans'); ?>

    <?= Component::Currency('form1', 'mycurrency')->currency('&pound;')->label('Pay this:'); ?>

    <?= Component::Date('form1', 'mydate')->label('My Date'); ?>

    <?= Component::DateTime('form1', 'mydatetime')->label('My Datetime'); ?>

    <?= Component::Display('form1', 'mydisplay')->value('something to show')->label('My Display'); ?>

    <?= Component::Email('form1', 'myemail')->value('wibble@hatstand.org')->label('My Email'); ?>

    <?= Component::Hidden('form1', 'myhidden')->value('hidden value') ?>

    <?= Component::Input('form1','myinput')->label('My Input') ?>

    <?= Component::InputButton('form1','myinputbutton')->label('My Input Button')->value('Click Me!') ?>

    <?= Component::Password('form1','mypassword')->label('My Password') ?>

    <?= Component::RadioButtonSet('form1', 'myradiobuttonset')->radioButtons(['one'=>'One','two'=>'Two','three'=>'Three'])->label("My Radio Button Set"); ?>

    <?= Component::RadioButtonSet('form1', 'myradiobuttonset2')->radioButtons(['three','four','five'])->setSelected('four')->label("My Radio Button Set 2"); ?>

    <?= Component::Select('form1', 'myselect')->options(['one'=>'One','two'=>'Two','three'=>'Three'])->label('My Select')->setSelected('two'); ?>

    <?= Component::Select('form1', 'myselect2')->optgroupOptions(['group 1'=>['one'=>'One','two'=>'Two','three'=>'Three'],'group 2'=>['four'=>'Four','five'=>'Five']])->label('My Select (optgroups)')->setSelected('four') ?>

    <?= Component::SelectMulti('form1', 'myselectmulti')->options(['one'=>'One','two'=>'Two','three'=>'Three'])->label('My Select Multui')->setSelected(['two','three'])?>

    <?= Component::SelectMulti('form1', 'myselectmulti2')->optgroupOptions(['group 1'=>['one'=>'One','two'=>'Two','three'=>'Three'],'group 2'=>['four'=>'Four','five'=>'Five']])->label('My Select Multi (optgroups)')->setSelected(['two','five']) ?>

    <?= Component::Submit('form1', 'mysubmitbutton')->value('My Submit Button') ?>

    <?= Component::TextArea('form1', 'mytextarea')->label('My Text Area') ?>

</form>
