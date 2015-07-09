<?php
namespace Admin;

use \WebTester;

class ApiKeysCest
{
    public function apiKeyListingTest(WebTester $I, \Codeception\Scenario $scenario)
    {
        $I = new WebTester\AdminSteps($scenario);
        $I->login();
        $I->click('API Keys');
        $I->seeInTitle('API Key Listing');
    }

    public function createApiKeyTest(WebTester $I, \Codeception\Scenario $scenario)
    {
        $I = new WebTester\AdminSteps($scenario);
        $I->login();
        $I->click('API Keys');
        $I->see('Add new API Key');
        $I->canSeeElement('button');
        $I->click('Add new API Key');
    }
}
