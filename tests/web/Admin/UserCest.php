<?php
namespace Admin;

use \WebTester;

class UserCest
{
    public function superAdminCanEditOtherSuperAdminTest(WebTester $I, \Codeception\Scenario $scenario)
    {
        $date = new \DateTime('now');
        $passwordChangeTime =  $date->format('Y-m-d H:m:s');

        $I = new WebTester\AdminSteps($scenario);
        $I->haveInDatabasePDOUser(
            'user',
            array(
                array(
                    'firstName' => 'superadmin1',
                    'lastName' => 'user',
                    'email' => 'superadmin1@flipit.com',
                    'password' => md5('Mind@123'),
                    'status' => 1,
                    'roleid' => 1,
                    'slug' => 'test-user-super123',
                    'popularKortingscode' => '0',
                    'passwordchangetime' => $passwordChangeTime
                ),
                array(
                    'firstName' => 'superadmin2',
                    'lastName' => 'user',
                    'email' => 'superadmin2@flipit.com',
                    'password' => md5('Mind@123'),
                    'status' => 1,
                    'roleid' => 1,
                    'slug' => 'test-user-super',
                    'popularKortingscode' => '0',
                    'passwordchangetime' => $passwordChangeTime
                )
            )
        );
        $I->haveInDatabasePDOUser(
            'ref_user_website',
            array(
                array(
                    'userid' => '365',
                    'websiteid' => '1'
                ),
                array(
                    'userid' => '366',
                    'websiteid' => '1'
                )
            )
        );

        $I->login('superadmin1@flipit.com', 'Mind@123');
        $I->see('superadmin1');
        $I->click('Gebruikers');
        $I->seeInCurrentUrl('admin/user');
        $I->waitForElementVisible('#userList tbody tr', 3);
        $I->click('superadmin2@flipit.com');
        $I->fillField('Voornaam', 'NewFirstName');
        $I->fillField('Nieuw wachtwoord', '');
        $I->click('Profiel aanpassen');
        $I->waitForElementVisible('#userList tbody tr', 20);
        $I->see('NewFirstName');
    }
}
