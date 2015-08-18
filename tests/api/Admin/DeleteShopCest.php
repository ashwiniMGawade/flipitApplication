<?php
namespace Admin;

use \ApiTester;

class DeleteShopCest
{
    protected $apiKey;

    public function _before(ApiTester $I)
    {
        $this->apiKey = '%25NWcIzZ6Oy9uXv7fKJBZE!5%24EEMN%245%26X';
    }

    public function testDeleteShop(ApiTester $I)
    {
        $I->wantTo('Delete shop by ID');
        $I->sendDelete('/shops/1?api_key='.$this->apiKey);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    public function testDeleteShopWithoutId(ApiTester $I)
    {
        $I->wantTo('Delete a shop without an ID.');
        $I->sendGet('/shops/?api_key='.$this->apiKey);
        $I->seeResponseCodeIs(404);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['message' => 'Not found']);
    }

    public function testDeleteShopWithStringId(ApiTester $I)
    {
        $I->wantTo('Delete a shop with an ID as string.');
        $I->sendGet('/shops/test?api_key='.$this->apiKey);
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['message' => 'Invalid shop Id']);
    }

    public function testDeleteShopWithInvalidId(ApiTester $I)
    {
        $I->wantTo('Delete a shop with invalid ID.');
        $I->sendGet('/shops/0?api_key='.$this->apiKey);
        $I->seeResponseCodeIs(404);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['message' => 'Shop not found']);
    }
}
