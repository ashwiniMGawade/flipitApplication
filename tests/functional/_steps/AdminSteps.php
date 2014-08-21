<?php
namespace FunctionalTester;

class AdminSteps extends \FunctionalTester
{
    public function login()
    {
        $config = \Codeception\Configuration::config();
        $I = $this;
        $I->amOnPage(\LoginPage::$URL);
        $I->fillField(\LoginPage::$usernameField, $config['adminSettings']['adminUsername']);
        $I->fillField(\LoginPage::$passwordField, $config['adminSettings']['adminPassword']);
        $I->click(\LoginPage::$formSubmitButton);
    }

    public function logout()
    {
        $I = $this;
    }
}
