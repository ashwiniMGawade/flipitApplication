<?php
namespace admin;
use \FunctionalTester;

class emailSettingsCest
{
    public function _before()
    {

    }

    public function _after()
    {
    }

    public function emailSettingsUpdate(FunctionalTester $I, \Codeception\Scenario $scenario)
    {
        $I = new FunctionalTester\AdminSteps($scenario);
        $I->login();
        $I->canSee('Scores');
        //$I->click('welkom');
        /*$I->fillField('#senderEmail', 'kim@web-flight.nl');
        $I->fillField('#senderName', 'kim');
        $I->click('button[type=submit]');
        $I->amOnPage('/admin/email/email-settings');
        $I->seeInField('#senderEmail', 'kim@web-flight.nl');
        $I->seeInField('#senderName', 'kim');*/
    }

    protected function emailSettingsValidation($I)
    {
        $this->fillForm($I, 'kim@web-flight.nl', 'Kim');
        $I->canSee('Sender email is valid.');
        //$I->canSee('Sender name is valid.');
    }

    protected function fillForm($I, $senderEmail, $senderName)
    {
        $I->fillField('#senderEmail', $senderEmail);
        $I->fillField('#senderName', $senderName);
        $I->click('button[type=submit]');
    }
}
