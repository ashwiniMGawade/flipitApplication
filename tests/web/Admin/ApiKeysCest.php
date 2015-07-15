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
        $I->amOnPage('/admin/apikeys');
        $I->click('Add new API Key');
        $I->wait(3);
        $I->see('Api Key has been successfully added');
        $I->click('Delete');
        $I->wait(1);
        $I->click('Yes');
        $I->wait(3);
        $I->see('Api Key has been deleted successfully');
    }
}
