<?php
namespace Admin;

use \WebTester;

class CodeAlertCest
{
    public function _before()
    {
        
    }

    public function _after()
    {
    }

    public function codealert(WebTester $I, \Codeception\Scenario $scenario)
    {
        $I = new WebTester\AdminSteps($scenario);
        $I->login();
        $I->canSee('Code alert');
        $I->click('Code alert');
        $I->amOnPage('/admin/email/code-alert');
        $I->canSee('Queued code alerts');
        $I->canSee('Code title');
        $I->canSee('Recipients count');
    }

    // public function codeAlertSettings(WebTester $I, \Codeception\Scenario $scenario)
    // {
    //     $I = new WebTester\AdminSteps($scenario);
    //     $I->login();
    //     $I->click('Code alert');
    //     $I->click('Code alert settings');
    //     $I->fillField('#emailSubject', 'email subject');
    //     $I->fillField('#emailHeader', 'email header');
    //     $I->click('button[type=submit]');
    //     $I->seeInField('#emailSubject', 'email subject');
    //     $I->seeInField('#emailHeader', 'email header');
    // }
}
