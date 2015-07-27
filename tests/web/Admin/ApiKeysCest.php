<?php
namespace Admin;

use \WebTester;

class ApiKeysCest
{
    public function apiKeyListingTest(WebTester $I, \Codeception\Scenario $scenario)
    {
        $I = new WebTester\AdminSteps($scenario);
        $this->seedApiKeysTable($I, 'zgzjK*y3^rSdh@!do1r&%f^so4@UMyXb');
        $I->login();
        $I->click('API Keys');
        $I->seeInTitle('API Key Listing');
        $I->waitForElementVisible('#ApiKeysListTbl tbody tr td');
        $I->see("zgzjK*y3^rSdh@!do1r&%f^so4@UMyXb");
    }

    public function createApiKeyTest(WebTester $I, \Codeception\Scenario $scenario)
    {
        $I = new WebTester\AdminSteps($scenario);
        $I->login();
        $I->amOnPage('/admin/apikeys');
        $I->click('Add new API Key');
        $I->waitForElementVisible('#ApiKeysListTbl tbody tr td');
        $I->waitForElementVisible('.success');
        $I->see('Api Key has been successfully added');
    }

    public function deleteApiKeyTest(WebTester $I, \Codeception\Scenario $scenario)
    {
        $I = new WebTester\AdminSteps($scenario);
        $I->login();
        $this->seedApiKeysTable($I, 'zgzjK*y3^rSdh@!do1r&%f^so4@UMyXb');
        $I->amOnPage('/admin/apikeys');
        $I->waitForElementVisible('#ApiKeysListTbl tbody tr td');
        $I->see("zgzjK*y3^rSdh@!do1r&%f^so4@UMyXb");
        $I->click('Delete');
        $I->waitForText('Are you sure you want to delete this Api Key?');
        $I->click('Yes');
        $I->waitForElementVisible('#ApiKeysListTbl tbody tr td');
        $I->waitForElementVisible('.success');
        $I->see('Api Key has been deleted successfully');
    }

    private function seedApiKeysTable($I, $apiKey)
    {
        $createdTime = date('Y-m-d H:i:s');
        $I->haveInDatabasePDOUser(
            'api_keys',
            array(
                'user_id' => 354,
                'api_key' => $apiKey,
                'deleted' => 0,
                'created_at' => $createdTime
            )
        );
    }
}
