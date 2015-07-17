<?php
namespace Admin;

use \WebTester;

class UserCest
{
    public function superAdminCanEditOtherSuperAdminTest(WebTester $I, \Codeception\Scenario $scenario)
    {
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
                    'popularKortingscode' => '0'
                ),
                array(
                    'firstName' => 'superadmin2',
                    'lastName' => 'user',
                    'email' => 'superadmin2@flipit.com',
                    'password' => md5('Mind@123'),
                    'status' => 1,
                    'roleid' => 1,
                    'slug' => 'test-user-super',
                    'popularKortingscode' => '0'
                )
            )
        );
        $I->login('superadmin1@flipit.com', 'Mind@123');
        $I->amOnPage('admin/');
        $I->amOnPage('admin/user/');
        $I->see('superadmin2@flipit.com');
    }
}
