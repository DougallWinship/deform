<?php
use \Deform\Component\ComponentFactory as Component;

if (isset($_POST) && count($_POST)) { ?>
<pre><?= serialize($_POST); ?></pre>
<?php } ?>

<h2>Components with Namespace 'form1'</h2>
<form method="post" enctype="multipart/form-data">
    <?= Component::Button('form1','mybutton')->value('buttonvalue')->html('press me!')->autofocus(true) ?>

    <?= Component::Checkbox('form1','mycheckbox')->text('check me!')->label('Check it out!') ?>

    <?= Component::CheckboxMulti('form1','mymulticheckbox')->checkboxes(['true'=>'True','false'=>"False",'File Not Found'])->label('New Booleans'); ?>

    <?= Component::Currency('form1', 'mycurrency')->currency('&pound;')->label('Pay this:'); ?>

    <?= Component::Date('form1','mydate')->label('My Date'); ?>

    <?= Component::DateTime('form1','mydatetime')->label('My Datetime'); ?>

    <?= Component::Display('form1','mydisplay')->value('something to show')->label('My Display'); ?>

    <?= Component::Email( 'form1','myemail')->value('wibble@hatstand.org')->label('My Email'); ?>

    <?= Component::File('form1', 'myfile')->label("My File") ?>

    <?= Component::Hidden( 'form1','myhidden')->value('hidden value') ?>

    <?= Component::Image('form1','myimage')->label("My Image") ?>

    <?= Component::InputButton('form1','myinputbutton')->label('My Input Button')->value('Click Me!') ?>

    <?= Component::MultipleEmail('form1','mymultiple-email')->label('My Multiple Email'); ?>

    <?= Component::MultipleFile('form1','mymultiple-file')->label('My Multiple File'); ?>

    <?= Component::Password('form1','mypassword')->label('My Password') ?>

    <?= Component::RadioButtonSet( 'form1','myradiobuttonset')->radioButtons(['one'=>'One','two'=>'Two','three'=>'Three'])->label("My Radio Button Set"); ?>

    <?= Component::RadioButtonSet('form1', 'myradiobuttonset2')->radioButtons(['three','four','five'])->setValue('four')->label("My Radio Button Set 2"); ?>

    <?= Component::Select( 'form1','myselect')->options(['one'=>'One','two'=>'Two','three'=>'Three'])->label('My Select')->setValue('two'); ?>

    <?= Component::Select( 'form1','myselect2')->optgroupOptions(['group 1'=>['one'=>'One','two'=>'Two','three'=>'Three'],'group 2'=>['four'=>'Four','five'=>'Five']])->label('My Select (optgroups)')->setValue('four') ?>

    <?= Component::SelectMulti('form1', 'myselectmulti')->options(['one'=>'One','two'=>'Two','three'=>'Three'])->label('My Select Multui')->setValue(['two','three'])?>

    <?= Component::SelectMulti( 'form1','myselectmulti2')->optgroupOptions(['group 1'=>['one'=>'One','two'=>'Two','three'=>'Three'],'group 2'=>['four'=>'Four','five'=>'Five']])->label('My Select Multi (optgroups)')->setValue(['two','five']) ?>

    <?= Component::Slider('form1','myslider')->label('My Slider') ?>

    <?= Component::Submit( 'form1','mysubmitbutton')->value('My Submit Button') ?>

    <?= Component::Text('form1','mytext')->label('My Text') ?>

    <?= Component::TextArea('form1', 'mytextarea')->label('My Text Area') ?>

</form>

