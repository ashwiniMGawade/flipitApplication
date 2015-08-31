<?php
namespace Admin;

use \WebTester;

class ShopCest
{
    public function createShopWithClassifiactionTest(WebTester $I, \Codeception\Scenario $scenario)
    {
        $I = new WebTester\AdminSteps($scenario);
        $I->login();
        $I->click('Winkels');
        $I->click('Voeg nieuwe winkel toe');
        $I->fillField('Shopnaam', 'My Test Shop');
        $I->fillField('Navigatie URL', 'My-Test-Shop');
        $I->fillField('#shopTitle', 'My Test Shop');
        $I->fillField('#shopSubTitle', 'My Test Shop Sub title');
        $I->selectOption('#selecteditors', 'Caroline Westendorp');
        $I->selectOption('#selectClassification', 'AA+');
        $I->fillField('shopRefUrl', 'http://test.com');
        $I->fillField('shopActualUrl', 'http://test.com');
        $I->click('#categoryBtn-1');
        $I->click('button#publishShopButton');
        $I->waitForElementVisible('.success', 10);
        $I->see('Deze shop is met succes opgeslagen!');
        $I->see('My Test Shop');
        $I->see('AA+');
    }

    public function editShopWithClassifiactionTest(WebTester $I, \Codeception\Scenario $scenario)
    {
        $I = new WebTester\AdminSteps($scenario);
        $I->login();
        $I->click('Winkels');
        $I->waitForText('Acceptance shop');
        $I->click('Acceptance shop');
        $I->fillField('Shopnaam', 'My Test Shop');
        $I->selectOption('#selecteditors', 'Caroline Westendorp');
        $I->selectOption('#selectClassification', 'AAA');
        $I->fillField('#howToPageSlug', 'page-slug');
        $I->click('Publiceer Winkel');
        $I->waitForText('Voeg nieuwe winkel toe');
        $I->see('Deze shop is met succes geüpdatet!');
        $I->see('My Test Shop');
        $I->see('AAA');
    }

    public function viewShopClassificationTest(WebTester $I, \Codeception\Scenario $scenario)
    {
        $I = new WebTester\AdminSteps($scenario);
        $I->login();
        $I->click('Winkels');
        $I->waitForText('Acceptance shop');
        $I->see('Rating');
        $I->click('Acceptance shop');
        $I->see('Shop Rating : A');
        $I->amOnPage('admin/offer/editoffer/id/1');
        $I->waitForText('Shop Rating : A');
    }
}
