<?php
namespace Guest;
use \WebTester;

class ShopSearchCest
{
    public function testShopSearch(WebTester $I, \Codeception\Scenario $scenario)
    {
        $I->wantTo('Search hotels.com in the search box');
        $I->amOnPage('/');
        $I->see('ZOEK');
        $I->fillField('#searchFieldHeader', 'Hotels');
        $I->wait(1);
        $I->see('Hotels.com');
    }
}
