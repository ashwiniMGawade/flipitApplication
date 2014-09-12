<?php
use \AcceptanceTester;

class codealerttestCest
{
    public function _before()
    {
    }

    public function _after()
    {
    }

    // tests
    public function codealertemailTest(AcceptanceTester $I, \Codeception\Scenario $scenario)
    {
        $I = new AcceptanceTester\LoginSteps($scenario);
        $I->login();
        $I->canSee('Code alert');
        $I->click('Code alert');
        $I->amOnPage('/admin/email/code-alert');
        $I->canSee('Code alert settings');
        $I->click('Code alert settings');
        $I->amOnPage('/admin/email/code-alert-settings');
        $I->click('.select2-choice');
        $I->click('#sendTest');
        $I->wait(10);
        $I->click('Yes');
        $I->wait(10);
        $I->seeInCurrentUrl('/admin/email/codealert/send/test');
    }
}
