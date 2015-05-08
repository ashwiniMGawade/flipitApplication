<?php
namespace FunctionalTester;

class AdminSteps extends \FunctionalTester
{
    public function login()
    {
        $config = \Codeception\Configuration::config();
        $I = $this;
        $this->addUser();
        $I->amOnPage(\LoginPage::$URL);
        $I->fillField(\LoginPage::$usernameField, 'test@flipit.com');
        $I->fillField(\LoginPage::$passwordField, 'Mind@123');
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
            'status' => '1',
            'role' => '1',
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
        // $user = new \User();
        // $user->truncateTables();
        // $role = new \Role();
        // $role->addUserRoles();
        // $rights = new \Rights();
        // $rights->addRights();
        // $user->addUser($userParameters, 'test.png');
    }
}
