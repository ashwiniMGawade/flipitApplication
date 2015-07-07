<?php
namespace Admin;

use \ApiTester;

class GetShopCest
{
    // tests
    public function getShop(ApiTester $I)
    {
        $I->wantTo('Get shop by ID');
        $I->sendGet('/shop/1s');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
}
