<?php
namespace Admin;

use \ApiTester;

class GetShopCest
{

    public function testGetShop(ApiTester $I)
    {
        $I->wantTo('Get shop by ID');
        $I->sendGet('/shops/1');
        // Add API key validation case here
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    public function testGetShopWithoutId(ApiTester $I)
    {
        $I->wantTo('Get a shop without an ID.');
        $I->sendGet('/shops/');
        $I->seeResponseCodeIs(404);
    }

    public function testGetShopWithStringId(ApiTester $I)
    {
        $I->wantTo('Get a shop with an ID as string.');
        $I->sendGet('/shops/test');
        $I->seeResponseCodeIs(404);
    }

    public function testGetShopWithInvalidId(ApiTester $I)
    {
        $I->wantTo('Get a shop with invalid ID.');
        $I->sendGet('/shops/0');
        $I->seeResponseCodeIs(404);
    }
}
