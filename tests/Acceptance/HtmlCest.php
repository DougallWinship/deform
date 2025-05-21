<?php
namespace App\Tests\Acceptance;

use \AcceptanceTester;

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
        $I->seeElement(['css'=>'div.outerdiv']);
        $I->seeElement(['css'=>'hr.innerhr']);
    }
}
