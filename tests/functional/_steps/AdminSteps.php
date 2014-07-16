<?php
namespace FunctionalTester;

class AdminSteps extends \FunctionalTester
{
    public function login($name, $password)
    {
        $I = $this;
        $I->amOnPage(\LoginPage::$URL);
        $I->fillField(\LoginPage::$usernameField, $name);
        $I->fillField(\LoginPage::$passwordField, $password);
        $I->click(\LoginPage::$formSubmitButton);
    }

    public function logout()
    {
        $I = $this;
    }
}
