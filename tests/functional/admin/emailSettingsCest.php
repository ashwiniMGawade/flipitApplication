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
        $I->login('kim@web-flight.nl', 'Mind@123');
        $I->click('Email Settings');
        $this->fillForm($I, 'kim@web-flight.nl', 'Kim');
        //$I->seeInField('#senderEmail', 'kim@web-flight.nl');
        //$I->canSee('Email settings have succesfully been updated.');
    }

    // public function emailSettingsValidation(FunctionalTester $I, \Codeception\Scenario $scenario)
    // {
    //     $this->fillForm('noEmailAddress', '');
    //     $I->canSee('Sender email is not valid.');
    //     $I->canSee('Sender name cannot be empty.');
    // }

    protected function fillForm($I, $senderEmail, $senderName)
    {
        $I->fillField('#senderEmail', $senderEmail);
        $I->fillField('#senderName', $senderName);
        //$I->click('button type["submit"]');
        //$I->amOnPage('admin/email-settings');
    }
}
