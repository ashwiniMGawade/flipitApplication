<?php
namespace Admin;

use \WebTester;

class OfferCest
{
    public function _before(WebTester $I, \Codeception\Scenario $scenario)
    {
        $I = new WebTester\AdminSteps($scenario);
        $I->login();
        $I->canSee('Acties');
        $I->click('Acties');
    }

    public function _after()
    {
    
    }

    public function createOffer(WebTester $I, \Codeception\Scenario $scenario)
    {
        $I->canSee('Add New Offer');
        $I->click('Add New Offer');
        $I->canSeeInCurrentUrl('offer/addoffer');
        $this->fillForm($I);
    }

    protected function fillForm($I)
    {
        $I->click('Select a Shop');
        $I->canSee('acceptance shop');
        $I->click('li.select2-highlighted');
        $I->wait(5);
        $I->fillField('#addofferTitle', 'functional test');
        $I->fillField('#couponCodeTxt', 'FUN234');
        $I->click('.liimg');
    }
}
