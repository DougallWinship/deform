<?php
namespace App\Tests\Acceptance;

use \AcceptanceTester;

class FormCest
{

    public function runCsrfSessionFormSubmission(AcceptanceTester $I)
    {
        $I->amOnPage("/form/session-run");

        $I->seeResponseCodeIs(200);
        $I->seeElement(['css' => 'input[name="example[csrf-token]"]']);
        $csrf = $I->grabAttributeFrom('input[name="example[csrf-token]"]', 'value');
        $I->seeElement(['css' => 'input[type=submit][value=submit]']);
        $I->click(['css' => 'input[type=submit][value=submit]']);

        $I->seeResponseCodeIs(200);
        $I->seeElement(['css' => 'pre.session']);
        $serialised = $I->grabTextFrom('pre.session', 'value');
        $data = unserialize($serialised);
        $I->assertArrayHasKey('example-csrf-token', $data);
        $I->assertEquals($csrf, $data['example-csrf-token']);
    }

    public function runBadCsrfSessionFormSubmission(AcceptanceTester $I)
    {
        $I->amOnPage("/form/session-run");

        $I->seeResponseCodeIs(200);
        $I->seeElement(['css' => 'input[name="example[csrf-token]"]']);
        $I->fillField(['name' => 'example[csrf-token]'], 'BADTOKEN');
        $I->seeElement(['css' => 'input[type=submit][value=submit]']);
        $I->click(['css' => 'input[type=submit][value=submit]']);

        $I->seeResponseCodeIs(403);
        $I->see("Forbidden");
    }

    public function runCsrfCookieFormSubmission(AcceptanceTester $I)
    {
        $I->amOnPage("/form/cookie-run");
        $I->seeResponseCodeIs(200);

        $I->seeElement(['css' => 'input[name="example[csrf-token]"]']);
        $csrf = $I->grabAttributeFrom('input[name="example[csrf-token]"]', 'value');
        $I->seeElement(['css' => 'input[type=submit][value=submit]']);
        $I->click(['css' => 'input[type=submit][value=submit]']);

        $I->seeResponseCodeIs(200);
        $I->seeElement(['css' => 'pre.cookie']);
        $serialised = $I->grabTextFrom('pre.cookie', 'value');
        $data = unserialize($serialised);
        $I->assertArrayHasKey('example-csrf-token', $data);
        $I->assertEquals($csrf, $data['example-csrf-token']);
    }

    public function runBadCsrdCookieFormSubmission(AcceptanceTester $I)
    {
        $I->amOnPage("/form/cookie-run");

        $I->seeResponseCodeIs(200);
        $I->seeElement(['css' => 'input[name="example[csrf-token]"]']);
        $I->fillField(['name' => 'example[csrf-token]'], 'BADTOKEN');
        $I->seeElement(['css' => 'input[type=submit][value=submit]']);
        $I->click(['css' => 'input[type=submit][value=submit]']);

        $I->seeResponseCodeIs(403);
        $I->see("Forbidden");
    }
}