<?php
namespace App\Tests\Acceptance;

use \AcceptanceTester;

class IndexCest
{
    // tests
    public function canSeeHome(AcceptanceTester $I)
    {
        $I->amOnPage('/');
        $I->seeResponseCodeIs(200);
        $I->see('index.php');
    }
}
