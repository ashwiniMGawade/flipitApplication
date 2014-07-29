<?php
use \FunctionalTester;

class clickoutsCest
{
    public function _before()
    {
    }

    public function _after()
    {
    }

    // tests
    public function update(FunctionalTester $I, \Codeception\Scenario $scenario)
    {
         $I = new FunctionalTester\AdminSteps($scenario);
        $I->login('kim@web-flight.nl', 'Mind@123');
        $I->canSee('Email Settings');
        $I->click('Email Settings');
        $I->fillField('#senderEmail', 'kim@web-flight.nl');
        $I->fillField('#senderName', 'kimvvvvvvvvvvvvvv');
        $I->click('button[type=submit]');
        $I->amOnPage('/admin/email/email-settings');
        $I->seeInField('#senderEmail', 'kim@web-flight.nl');
        $I->seeInField('#senderEmail', 'kim@web-flight.nl');
        $I->seeInField('#senderName', 'kimvvvvvvvvvvvvvv');
    }
}