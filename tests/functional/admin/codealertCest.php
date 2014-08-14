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
    }
}
