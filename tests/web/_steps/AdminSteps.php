<?php
namespace WebTester;

class AdminSteps extends \WebTester
{
    public function login()
    {
        $I = $this;
        $config = \Codeception\Configuration::config();
        $this->addUser();
        $I->amOnPage(\LoginPage::$URL);
        $I->fillField(\LoginPage::$usernameField, 'test@flipit.com');
        $I->fillField(\LoginPage::$passwordField, 'password');
        $I->click(\LoginPage::$formSubmitButton);
    }

    public function logout()
    {
        $I = $this;
    }

    protected function addUser()
    {
        $userParameters = array(
            'firstName' => 'test',
            'lastName' => 'user',
            'email' => 'test@flipit.com',
            'password' => 'Mind@123',
            'status' => 1,
            'role' => 1,
            'slug' => 'test-user',
            'imageName' => 'test.png',
            'locale' => '',
            'nameStatus' => '0',
            'google' => '',
            'twitter' => '',
            'pintrest' => '',
            'likes' => '',
            'dislike' => '',
            'editortext' => '',
            'popularKortingscode' => '0'
        );
        /*$user = new \KC\Repository\User();
        //$user->truncateTables();
        $role = new \KC\Repository\Role();
        $role->addUserRoles();
        $rights = new \KC\Repository\Rights();
        $rights->addRights();
        $user->addUser($userParameters, 'test.png');
        //die('Hello');*/
    }
}
