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

    public function userShouldNotSeeThesePagesWhenRoleEqualsAdminTest(WebTester $I, \Codeception\Scenario $scenario)
    {
        $date = new \DateTime('now');
        $passwordChangeTime =  $date->format('Y-m-d H:m:s');

        $I = new WebTester\AdminSteps($scenario);
        $I->haveInDatabasePDOUser(
            'user',
            array(
                array(
                    'firstName' => 'admin',
                    'lastName' => 'user',
                    'email' => 'admin@flipit.com',
                    'password' => md5('Mind@123'),
                    'status' => 1,
                    'roleid' => 2,
                    'slug' => 'test-admin-123',
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
                )
            )
        );

        $I->login('admin@flipit.com', 'Mind@123');
        $I->see('admin');

        #Admin does not have access to Robots page
        $I->amOnPage('admin/robot');
        $I->see('Scores of the last 7 days');
        #Admin does not have access to Chains page
        $I->amOnPage('admin/chain');
        $I->see('Scores of the last 7 days');
        #Admin does not have access to Locale Settings page
        $I->amOnPage('admin/locale/locale-settings');
        $I->see('Scores of the last 7 days');
        #Admin does not have access to Email Settings page
        $I->amOnPage('admin/email/email-settings');
        $I->see('Scores of the last 7 days');
        #Admin does not have access to Import Shops page
        $I->amOnPage('admin/shop/importshops');
        $I->see('Voeg nieuwe winkel toe');
        #Admin does not have access to Visitors page
        $I->amOnPage('admin/visitor');
        $I->see('Scores of the last 7 days');
        #Admin does not have access to Ip Addresses page
        $I->amOnPage('admin/ipaddresses');
        $I->see('Scores of the last 7 days');
        #Admin does not have access to Users page
        $I->amOnPage('admin/user');
        $I->see('Scores of the last 7 days');
        #Admin does not have access to ApiKeys page
        $I->amOnPage('admin/apikeys');
        $I->see('Scores of the last 7 days');
    }

    public function userShouldNotSeeThesePagesWhenRoleEqualsEditorTest(WebTester $I, \Codeception\Scenario $scenario)
    {
        $date = new \DateTime('now');
        $passwordChangeTime =  $date->format('Y-m-d H:m:s');

        $I = new WebTester\AdminSteps($scenario);
        $I->haveInDatabasePDOUser(
            'user',
            array(
                array(
                    'firstName' => 'Editor',
                    'lastName' => 'user',
                    'email' => 'editor@flipit.com',
                    'password' => md5('Mind@123'),
                    'status' => 1,
                    'roleid' => 4,
                    'slug' => 'test-editor-123',
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
                )
            )
        );

        $I->login('editor@flipit.com', 'Mind@123');
        $I->see('Editor');

        #Editor does not have access to Robots page
        $I->amOnPage('admin/robot');
        $I->see('Scores of the last 7 days');
        #Editor does not have access to Chains page
        $I->amOnPage('admin/chain');
        $I->see('Scores of the last 7 days');
        #Editor does not have access to Locale Settings page
        $I->amOnPage('admin/locale/locale-settings');
        $I->see('Scores of the last 7 days');
        #Editor does not have access to Email Settings page
        $I->amOnPage('admin/email/email-settings');
        $I->see('Scores of the last 7 days');
        #Editor does not have access to Import Shops page
        $I->amOnPage('admin/shop/importshops');
        $I->see('Voeg nieuwe winkel toe');
        #Editor does not have access to Visitors page
        $I->amOnPage('admin/visitor');
        $I->see('Scores of the last 7 days');
        #Editor does not have access to Ip Addresses page
        $I->amOnPage('admin/ipaddresses');
        $I->see('Scores of the last 7 days');
        #Editor does not have access to Users page
        $I->amOnPage('admin/user');
        $I->see('Scores of the last 7 days');
        #Editor does not have access to ApiKeys page
        $I->amOnPage('admin/apikeys');
        $I->see('Scores of the last 7 days');
        #Editor does not have access to Account Settings page
        $I->amOnPage('admin/accountsetting');
        $I->see('Scores of the last 7 days');
        #Editor does not have access to Email Management page
        $I->amOnPage('admin/accountsetting/emailcontent');
        $I->see('Scores of the last 7 days');
        #Editor does not have access to Footer page
        $I->amOnPage('admin/footer');
        $I->see('Scores of the last 7 days');
        #Editor does not have access to Redirect page
        $I->amOnPage('admin/redirect');
        $I->see('Scores of the last 7 days');
        #Editor does not have access to Language page
        $I->amOnPage('admin/language');
        $I->see('Scores of the last 7 days');
        #Editor does not have access to Splash page
        $I->amOnPage('admin/splash');
        $I->see('Scores of the last 7 days');
    }
}
