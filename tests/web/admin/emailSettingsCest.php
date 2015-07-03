<?php
namespace admin;

use \WebTester;

class EmailSettingsCest
{
    public function _before()
    {

    }

    public function _after()
    {
    }

    public function emailSettingsUpdate(WebTester $I, \Codeception\Scenario $scenario)
    {
        $I = new WebTester\AdminSteps($scenario);
        $I->login();
        $I->canSee('Email Settings');
        $I->click('Email Settings');
        $I->fillField('#senderEmail', 'kim@web-flight.nl');
        $I->fillField('#senderName', 'kim');
        $I->click('button[type=submit]');
        $I->amOnPage('/admin/email/email-settings');
        $I->seeInField('#senderEmail', 'kim@web-flight.nl');
        $I->seeInField('#senderName', 'kim');
    }
}
