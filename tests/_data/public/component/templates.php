<?php
if (isset($_POST) && count($_POST)) { ?>
    <pre><?= serialize($_POST); ?></pre>
    <pre><?= print_r($_POST,true); ?></pre>
<?php } ?>

<script>
<?php
echo \Deform\Component\ComponentFactory::getCustomElementDefinitionsJavascript();
?>
</script>
<form name="potatoes" data-namespace="potatoes" method="post" action="">
    <deform-button name='button1' value='buttonvalue' label="buttonLabel" onclick="this.parentNode.submit()">Button</deform-button><br>
    <deform-checkbox name='checkbox1' value="checkboxvalue" label="Checkbox Label" text="Check me?" checked></deform-checkbox><br>
    <deform-checkbox-multi name='checkbox-multi1' value='{"one":"One","two":"Two","three":"Three"}' checked='["one","two"]' label='CheckboxMulti Label'></deform-checkbox-multi><br>
    <deform-color-selector name='color-selector1' label='Colour Selector Label'></deform-color-selector>
    <deform-currency name='currency1' currency="&pound;" label='Currency Label'></deform-currency><br>
    <deform-date name='date1' label='Date Label' value="2012-12-12"></deform-date><br>
    <deform-date-time name='datetime1' label='DateTime Label' value="2012-12-12T10:10"></deform-date-time><br>
    <deform-display name='display1' label='Display Label' value='show this'></deform-display><br>
    <deform-email name='email1' label='Component Email' value='potatoes'></deform-email><br>
    <deform-file name='file1' label='Component File'></deform-file><br>
    <deform-image name='image1' label='Component Image'></deform-image><br>
    <deform-multiple-file name='multiplefile1' label='Component Multiple File'></deform-multiple-file><br>
    <deform-multiple-email name='multipleemail1' label='Component Multiple Email'>Button</deform-multiple-email><br>
    <deform-hidden name='hidden1' value='hiddenvalue'></deform-hidden> &laquo;Hidden Input<br><br>
    <deform-input-button name='inputbutton1' label='Component Input Button' value='value1' label='Input Button Label'></deform-input-button><br>
    <deform-password name='password1' label='Component Password' value='password1' label='Password Label'></deform-password><br>
    <deform-radio-button-set name='radiobuttonset1' label='Component Radio Button Set' options='{"one":"One","two":"Two","three":"Three"}' checked='two' label='Radio Buton Set Label'></deform-radio-button-set><br>
    <deform-select name='select1' label="Component Select" options='{"one":"One","two":"Two","three":"Three"}' selected='two' label='Select Label'></deform-select>
    <deform-select-multi name='selectmulti1' options='{"one":"One","two":"Two","three":"Three"}' selected='["one","three"]' label='Select Multi'></deform-select-multi>
    <deform-slider name='slider1' label='Slider Label' min="50" max="150" showOutput="true" value="100"></deform-slider><br>
    <deform-text name='text1' label='Text Label' value='text value'></deform-text><br>
    <deform-text-area name='textarea1' label='Component Text Area'>this is some text area value</deform-text-area><br>
    <deform-submit name='submit1' value="potatoes" onclick="this.parentNode.submit()"></deform-submit><br>
</form>