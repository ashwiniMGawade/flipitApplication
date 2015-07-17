<?php
namespace Admin;

use \ApiTester;

class DeleteShopCest
{
    public function testDeleteShop(ApiTester $I)
    {
        $I->wantTo('Delete shop by ID');
        $I->sendDelete('/shops/203');
        // Add API key validation case here
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    public function testDeleteShopWithoutId(ApiTester $I)
    {
        $I->wantTo('Delete a shop without an ID.');
        $I->sendGet('/shops/');
        $I->seeResponseCodeIs(404);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['message' => 'Not found']);
    }

    public function testDeleteShopWithStringId(ApiTester $I)
    {
        $I->wantTo('Delete a shop with an ID as string.');
        $I->sendGet('/shops/test');
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['message' => 'Invalid shop Id']);
    }

    public function testDeleteShopWithInvalidId(ApiTester $I)
    {
        $I->wantTo('Delete a shop with invalid ID.');
        $I->sendGet('/shops/0');
        $I->seeResponseCodeIs(404);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['message' => 'Shop not found']);
    }
}
