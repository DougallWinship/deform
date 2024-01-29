<script>
<?php
echo \Deform\Component\ComponentFactory::getCustomElementDefinitionsJavascript();
?>
</script>
<form name="potatoes" data-namespace="potatoes">
<component-button name='button1' value='buttonvalue' label='Button Label' onclick="this.parentNode.submit()">Button</component-button><br>
<component-checkbox name='checkbox1' value="checkboxvalue" label="Checkbox Label"></component-checkbox><br>
<component-checkbox-multi name='checkbox-multi1' values='{"one":"One","two":"Two","three":"Three"}' label='CheckboxMulti Label'></component-checkbox-multi><br>
<component-currency name='currency1' currency="&pound;" label='Currency Label'></component-currency><br>
<component-date name='date1' label='Date Label'></component-date><br>
<component-date-time name='datetime1' label='DateTime Label'></component-date-time><br>
<component-display name='display1' label='Display Label' value='show this'></component-display><br>
<component-email name='email1' label='Component Email' value='potatoes'></component-email><br>
<component-file name='file1' label='Component File'></component-file><br>
<component-image name='image1' label='Component Image'></component-image><br>
<component-multiple-file name='multiplefile1' label='Component Multiple File'></component-multiple-file><br>
<component-multiple-email name='multipleemail1' label='Component Multiple Email'>Button</component-multiple-email><br>
<component-hidden name='hidden1' value='hiddenvalue'></component-hidden> &laquo;Hidden Input<br><br>
<component-input-button name='inputbutton1' label='Component Input Button' value='value1' label='Input Button Label'></component-input-button><br>
<component-password name='password1' label='Component Password' value='password1' label='Password Label'></component-password><br>
<component-radio-button-set name='radiobuttonset1' label='Component Radio Button Set' values='{"one":"One","two":"Two","three":"Three"}' label='Radio Buton Set Label'></component-radio-button-set><br>
<component-select name='select1' label="component-select" options='{"one":"One","two":"Two","three":"Three"}' label='Select Label'></component-select>
<component-select-multi name='selectmulti1' options='{"one":"One","two":"Two","three":"Three"}' label='Select Multi'></component-select-multi>
<component-slider name='slider1' label='Slider Label' min="50" max="150" showOutput="true"></component-slider><br>
<component-submit name='submit1' value="potatoes"></component-submit><br>
<component-text name='text1' label='Text Label' value='text value'></component-text><br>
<component-text-area name='textarea1' label='component-text-area'>this is some text area value</component-text-area><br>
</form>