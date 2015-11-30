<?php
namespace System;

use \ApiTester;

class AuthenticationCest
{
    protected $apiKey;

    public function _before(ApiTester $I)
    {
        $this->apiKey = $I->getConfig('apiKey');
    }

    public function testApiKeyAuthenticationWithoutApiKey(ApiTester $I)
    {
        $I->wantTo('Try to get shop without API key');
        $I->sendGet('/shops/1');
        $I->seeResponseCodeIs(401);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['message' => 'API key is required.']);
    }

    public function testApiKeyAuthenticationWithAnInvalidApiKey(ApiTester $I)
    {
        $I->wantTo('Try to get shop with an invalid API key');
        $I->sendGet('/shops/1?api_key=TEST_KEY');
        $I->seeResponseCodeIs(401);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['message' => 'Invalid API key.']);
    }

    public function testApiKeyAuthenticationWithValidApiKey(ApiTester $I)
    {
        $I->wantTo('Try to get shop with valid API key');
        $I->sendGet('/shops/1?api_key='.$this->apiKey);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
}
