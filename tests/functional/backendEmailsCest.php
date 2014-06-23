<?php
use \TestGuy;

class backendEmailsCest
{

    public function _before()
    {
    }

    public function _after()
    {
    }

    public function manageEmailTemplates(TestGuy $I)
    {
        TestCommons::logInAdmin($I);
        $I->wantTo('See the email templates.');
        $I->amOnPage('/admin/emails/');
        $I->see('Name', 'tr');
        $I->see('Last send date', 'tr');
        $I->see('Number of emails send', 'tr');
    }

    public function successfullyChangeEmailSettings(TestGuy $I)
    {
        $I->wantTo('Successfully Change email settings');
        $email = null;
        $name = 'flipit';
        self::fillEmailSettingsForm($email, $name, $I);
        $I->seeInField('senderEmail', 'true@flipit.com');
        $I->seeInField('senderName', 'flipit');
        $I->see('The emailsettings have been succesfully changed.');
    }

    public function failToChangeEmailSettings(TestGuy $I)
    {
        $I->wantTo('Fail to Change email settings');
        $email = 'false';
        $name = '';
        self::fillEmailSettingsForm($email, $name, $I);
        $I->see('Email address is not valid.');
        $I->see('Name cannot be blank.');
    }

    public function invalidNameEmailSettings(TestGuy $I)
    {
        $I->wantTo('Add invalid name in email settings');
        $email = null;
        $name = '33333';
        self::fillEmailSettingsForm($email, $name, $I);
        $I->see('Name can not be a number.');
    }

    protected static function fillEmailSettingsForm($email, $name, $I)
    {
        //$I = new TestGuy();
        TestCommons::logInAdmin($I);
        $I->amOnPage('/admin/emailsettings/');
        $I->submitForm('#emailSettings', array(
                'senderEmail' => isset($email) ? $email : 'true@flipit.com',
                'senderName' => $name
        ));

        return true;
    }
}
