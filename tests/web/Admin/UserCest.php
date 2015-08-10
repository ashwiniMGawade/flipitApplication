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
        $this->seedRefUserWebsiteTable($I);

        $I->login('admin@flipit.com', 'Mind@123');
        $I->see('admin');

        $pages = array(
            'admin/robot',
            'admin/chain',
            'admin/locale/locale-settings',
            'admin/email/email-settings',
            'admin/visitor',
            'admin/ipaddresses',
            'admin/user',
            'admin/apikeys'
        );

        $redirectsTo = '/admin';

        $this->testTryingToAccessPageRedirectsToURL($I, $pages, $redirectsTo);
        $this->testTryingToAccessShopsImportPageRedirectsToShopPage($I);
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
        $this->seedRefUserWebsiteTable($I);

        $I->login('editor@flipit.com', 'Mind@123');
        $I->see('Editor');

        $pages = array(
            'admin/robot',
            'admin/chain',
            'admin/locale/locale-settings',
            'admin/email/email-settings',
            'admin/visitor',
            'admin/ipaddresses',
            'admin/user',
            'admin/apikeys',
            'admin/accountsetting',
            'admin/accountsetting/emailcontent',
            'admin/footer',
            'admin/redirect',
            'admin/language',
            'admin/splash'
        );

        $redirectsTo = '/admin';

        $this->testTryingToAccessPageRedirectsToURL($I, $pages, $redirectsTo);
        $this->testTryingToAccessShopsImportPageRedirectsToShopPage($I);
    }

    public function userShouldSeeLanguageFilePageWhenRoleEqualsAdmin(WebTester $I, \Codeception\Scenario $scenario)
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
        $this->seedRefUserWebsiteTable($I);

        $I->login('admin@flipit.com', 'Mind@123');
        $I->see('admin');
        $I->amOnPage('admin/language');
        $I->see('Start Inline Translation');
    }

    private function testTryingToAccessPageRedirectsToURL($I, $pages, $redirectsTo)
    {
        foreach ($pages as $page) {
            $I->amOnPage($page);
            $I->seeCurrentUrlEquals($redirectsTo);
        }
    }

    private function testTryingToAccessShopsImportPageRedirectsToShopPage($I)
    {
        #Non Super Admin users dont not have access to Import Shops page
        $I->amOnPage('admin/shop/importshops');
        $I->seeCurrentUrlEquals('/admin/shop');
        $I->see('Voeg nieuwe winkel toe');
    }

    private function seedRefUserWebsiteTable($I)
    {
        $I->haveInDatabasePDOUser(
            'ref_user_website',
            array(
                array(
                    'userid' => '365',
                    'websiteid' => '1'
                )
            )
        );
    }
}
