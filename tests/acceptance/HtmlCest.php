<?php

class HtmlCest
{
    public function _before(AcceptanceTester $I)
    {
        $I->amOnPage("/html/build");
        $I->seeResponseCodeIs(200);
    }

    // tests
    public function seeHtml(AcceptanceTester $I)
    {
        $I->seeElement(['css'=>'div']);
    }
}
