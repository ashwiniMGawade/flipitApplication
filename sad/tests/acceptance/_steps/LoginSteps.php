<?php
namespace AcceptanceTester;

class LoginSteps extends \AcceptanceTester
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
        $I = $this;
        $I->initializeDb('Db', $I->flipitTestUserDb());

        $I->haveInDatabase(
            'role',
            array(
            'id' => 1,
            'name' => 'Super Administrator'
            )
        );
        $I->haveInDatabase(
            'rights',
            array(
            'id' => 1,
            'name' => 'administration',
            'rights' => 1,
            'roleid' => 1
            )
        );
        $I->haveInDatabase(
            'user',
            array(
            'firstname' => 'test',
            'lastname' => 'user',
            'email' => 'test@flipit.com',
            'password' => md5('Mind@123'),
            'status' => '1',
            'roleid' => '1',
            'slug' => 'test-user',
            'passwordchangetime' => date('Y-m-d H:i:s')
            )
        );
    }
}
