<?php
namespace Admin;

use \WebTester;

class ApiKeysCest
{
    public function _before(WebTester $I)
    {
    }

    public function _after(WebTester $I)
    {
    }

    public function apiKeyListingTest(WebTester $I, \Codeception\Scenario $scenario)
    {
        $I = new WebTester\AdminSteps($scenario);
        $I->login();
        $I->click('API Keys');
        $I->see('wFWo((diu5xP;[_@R&nR>z+){g"78TVp');
    }
}
