<?php
namespace admin;
use \WebTester;

class codealertCest
{
    public function _before()
    {
        
    }

    public function _after()
    {
    }

    public function codealert(WebTester $I, \Codeception\Scenario $scenario)
    {
        $I->amOnPage('http://dev.flipit.com/in');
        $I->see('login');
        $I = new WebTester\AdminSteps($scenario);
        $I->login();
        $I->canSee('Code alert');
        $I->click('Code alert');
        $I->amOnPage('/admin/email/code-alert');
        $I->canSee('Queued code alerts');
        $I->canSee('Code title');
        $I->canSee('Recipients count');
        $I->click('.logout');
    }

    public function codeAlertSettings(WebTester $I, \Codeception\Scenario $scenario)
    {
        $I = new WebTester\AdminSteps($scenario);
        $I->login();
        $I->canSee('Code alert');
        $I->click('Code alert');
        $I->amOnPage('/admin/email/code-alert');
        $I->canSee('Code alert settings');
        $I->click('Code alert settings');
        $I->amOnPage('/admin/email/code-alert-settings');
        $I->fillField('#emailSubject', 'email subject');
        $I->fillField('#emailHeader', 'email header');
        $I->click('button[type=submit]');
        $I->amOnPage('/admin/email/code-alert-settings');
        $I->seeInField('#emailSubject', 'email subject');
        $I->seeInField('#emailHeader', 'email header');
    }
}
