<?php
namespace admin;
use \FunctionalTester;

class codealertCest
{
    public function _before()
    {
        
    }

    public function _after()
    {
    }

    // tests
    public function codealert(FunctionalTester $I, \Codeception\Scenario $scenario)
    {
        $I = new FunctionalTester\AdminSteps($scenario);
        $I->login();
        $I->canSee('Code alert');
        $I->click('Code alert');
        $I->amOnPage('/admin/email/code-alert');
        $I->canSee('Queued code alerts');
        $I->canSee('Code title');
        $I->canSee('Recipients count');
        $I->click('.logout');
    }

    public function codeAlertSettings(FunctionalTester $I, \Codeception\Scenario $scenario)
    {
        $I = new FunctionalTester\AdminSteps($scenario);
        $I->login();
        $I->canSee('Code alert');
        $I->click('Code alert');
        $I->amOnPage('/admin/email/code-alert');
        $I->canSee('Code alert settings');
        $I->click('Code alert settings');
        $I->amOnPage('/admin/email/code-alert-settings');
        $I->seeInField('#emailSubject', '');
        $I->seeInField('#emailHeader', '');
    }
}
