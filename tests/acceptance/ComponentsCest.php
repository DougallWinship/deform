<?php

class ComponentsCest
{
    public function _before(AcceptanceTester $I)
    {
        $I->amOnPage("/components");
        $I->seeResponseCodeIs(200);
    }

    // tests
    public function button(AcceptanceTester $I)
    {
        $I->seeElement(['css' => 'div#form1-mybutton-container.component-container.container-type-button>div.control-container>button']);
        $I->dontSeeElement(['css' => 'div#form1-mybutton-container.component-container.component-type-button label']);
    }

    public function checkbox(AcceptanceTester $I)
    {
        $I->seeElement(['css' => 'div#form1-mycheckbox-container.component-container.container-type-checkbox>div.control-container>input[type=checkbox]']);
        $I->seeElement(['css' => 'div#form1-mycheckbox-container.component-container.container-type-checkbox>div.control-container>input[name*=expected_data]']);
        $I->seeElement(['css' => 'div#form1-mycheckbox-container.component-container.container-type-checkbox>div.control-container>label']);
    }

    public function checkboxMulti(AcceptanceTester $I)
    {
        $I->seeElement(['css' => 'div#form1-mymulticheckbox-container.component-container.container-type-checkbox-multi>div.label-container>label']);
        $I->seeElement(['css' => 'div#form1-mymulticheckbox-container.component-container.container-type-checkbox-multi>div.control-container>div.checkbox-wrapper>input[type=checkbox]']);
        $I->seeElement(['css' => 'div#form1-mymulticheckbox-container.component-container.container-type-checkbox-multi>div.control-container>div.checkbox-wrapper>label']);
    }

    public function currency(AcceptanceTester $I)
    {
        $I->seeElement(['css'=>'div#form1-mycurrency-container.component-container.container-type-currency>div.label-container>label']);
        $I->seeElement(['css'=>'div#form1-mycurrency-container.component-container.container-type-currency>div.control-container>label.currency-symbol']);
        $I->seeElement(['css'=>'div#form1-mycurrency-container.component-container.container-type-currency>div.control-container>input[type=text]']);
    }

    public function date(AcceptanceTester $I)
    {
        $I->seeElement(['css'=>'div#form1-mydate-container.component-container.container-type-date>div.label-container>label']);
        $I->seeElement(['css'=>'div#form1-mydate-container.component-container.container-type-date>div.control-container>input[type=date]']);
    }

    public function datetime(AcceptanceTester $I)
    {
        $I->seeElement(['css'=>'div#form1-mydatetime-container.component-container.container-type-date-time>div.label-container>label']);
        $I->seeElement(['css'=>'div#form1-mydatetime-container.component-container.container-type-date-time>div.control-container>input[type=datetime-local]']);
    }

    public function display(AcceptanceTester $I)
    {
        $I->seeElement(['css'=>'div#form1-mydisplay-container.component-container.container-type-display>div.label-container>label']);
        $I->seeElement(['css'=>'div#form1-mydisplay-container.component-container.container-type-display>div.control-container>input[disabled=disabled][type=text]']);
    }

    public function hidden(AcceptanceTester $I)
    {
        $I->seeElement(['css'=>'input#hidden-form1-myhidden[type=hidden][value="hidden value"]']);
    }

    public function input(AcceptanceTester $I)
    {
        $I->seeElement(['css'=>'div#form1-myinput-container.component-container.container-type-input>div.label-container>label']);
        $I->seeElement(['css'=>'div#form1-myinput-container.component-container.container-type-input>div.control-container>input[type=text]']);
    }

    public function inputButton(AcceptanceTester $I)
    {
        $I->seeElement(['css'=>'div#form1-myinputbutton-container.component-container.container-type-input-button>div.label-container>label']);
        $I->seeElement(['css'=>'div#form1-myinputbutton-container.component-container.container-type-input-button>div.control-container>input[type=button]']);
    }

    public function password(AcceptanceTester $I)
    {
        $I->seeElement(['css'=>'div#form1-mypassword-container.component-container.container-type-password>div.label-container>label']);
        $I->seeElement(['css'=>'div#form1-mypassword-container.component-container.container-type-password>div.control-container>input[type=password]']);
    }

    public function radioButtonSet(AcceptanceTester $I)
    {
        $I->seeElement(['css'=>'div#form1-myradiobuttonset-container.component-container.container-type-radio-button-set>div.label-container>label']);
        $I->seeElement(['css'=>'div#form1-myradiobuttonset-container.component-container.container-type-radio-button-set>div.control-container>div.radio-button-container>input[type=radio]']);
        $I->seeElement(['css'=>'div#form1-myradiobuttonset-container.component-container.container-type-radio-button-set>div.control-container>div.radio-button-container>label']);
    }

    public function radioButtonSet2(AcceptanceTester $I)
    {
        $I->seeElement(['css'=>'div#form1-myradiobuttonset2-container.component-container.container-type-radio-button-set>div.label-container>label']);
        $I->seeElement(['css'=>'div#form1-myradiobuttonset2-container.component-container.container-type-radio-button-set>div.control-container>div.radio-button-container>input#radiobuttonset-form1-myradiobuttonset2-four[type=radio][checked=checked]']);
        $I->seeElement(['css'=>'div#form1-myradiobuttonset2-container.component-container.container-type-radio-button-set>div.control-container>div.radio-button-container>label']);
    }

    public function select(AcceptanceTester $I)
    {
        $I->seeElement(['css'=>'div#form1-myselect-container.component-container.container-type-select>div.label-container>label']);
        $I->seeElement(['css'=>'div#form1-myselect-container.component-container.container-type-select>div.control-container>select>option[value=two][selected=selected]']);
    }

    public function selectOptgroups(AcceptanceTester $I)
    {
        $I->seeElement(['css'=>'div#form1-myselect2-container.component-container.container-type-select>div.label-container>label']);
        $I->seeElement(['css'=>'div#form1-myselect2-container.component-container.container-type-select>div.control-container>select>optgroup[label="group 2"]>option[value=four][selected=selected]']);
    }

    public function selectMulti(AcceptanceTester $I)
    {
        $I->seeElement(['css'=>'div#form1-myselectmulti-container.component-container.container-type-select-multi>div.label-container>label']);
        $I->seeElement(['css'=>'div#form1-myselectmulti-container.component-container.container-type-select-multi>div.control-container>select[multiple=multiple]>option[value=two][selected=selected]']);
        $I->seeElement(['css'=>'div#form1-myselectmulti-container.component-container.container-type-select-multi>div.control-container>select[multiple=multiple]>option[value=three][selected=selected]']);
    }

    public function selectMultiOptgroups(AcceptanceTester $I)
    {
        $I->seeElement(['css'=>'div#form1-myselectmulti2-container.component-container.container-type-select-multi>div.label-container>label']);
        $I->seeElement(['css'=>'div#form1-myselectmulti2-container.component-container.container-type-select-multi>div.control-container>select[multiple=multiple]>optgroup[label="group 1"]>option[value=two][selected=selected]']);
        $I->seeElement(['css'=>'div#form1-myselectmulti2-container.component-container.container-type-select-multi>div.control-container>select[multiple=multiple]>optgroup[label="group 2"]>option[value=five][selected=selected]']);
    }

    public function submitButton(AcceptanceTester $I)
    {
        $I->seeElement(['css'=>'input#submit-form1-mysubmitbutton[type=submit][value="My Submit Button"]']);

    }

    public function textarea(AcceptanceTester $I)
    {
        $I->seeElement(['css'=>'div#form1-mytextarea-container.component-container.container-type-text-area>div.label-container>label']);
        $I->seeElement(['css'=>'div#form1-mytextarea-container.component-container.container-type-text-area>div.control-container>textarea']);
    }

    public function submitForm(AcceptanceTester $I)
    {
        $I->click(['id'=>'submit-form1-mysubmitbutton']);
        $I->seeElement(['css'=>"pre"]);
        $postDataString = $I->grabTextFrom(['css'=>"pre"]);
        $postData = unserialize($postDataString);
        $I->assertArrayHasKey('form1',$postData);
        $form1Data = $postData['form1'];
        $I->assertArrayHasKey('expected_data', $form1Data);
        $expectedData = $form1Data['expected_data'];
        $I->assertContains('mycheckbox', $expectedData);
        $I->assertContains('mymulticheckbox', $expectedData);
        $I->assertContains('myradiobuttonset', $expectedData);
        $I->assertContains('myradiobuttonset2', $expectedData);

        $I->assertArrayContainsSubset([
            'mycurrency'=>'',
            'mydate'=>'',
            'mydatetime'=>'',
            'myemail'=>'wibble@hatstand.org',
            'myhidden'=>'hidden value',
            'myinput'=>'',
            'mypassword'=>'',
            'myradiobuttonset2'=>'four',
            'myselect'=>'two',
            'myselect2'=>'four',
            'mytextarea'=>'',
            'mysubmitbutton'=>'My Submit Button'
        ],$form1Data);

        $I->assertArrayHasKey('myselectmulti', $form1Data);
        $I->assertContains('two', $form1Data['myselectmulti']);
        $I->assertContains('three', $form1Data['myselectmulti']);
        $I->assertArrayHasKey('myselectmulti2', $form1Data);
        $I->assertContains('two', $form1Data['myselectmulti2']);
        $I->assertContains('five', $form1Data['myselectmulti2']);
    }
}
