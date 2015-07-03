<?php
namespace admin;
use \FunctionalTester;

class offerCest
{
    public function _before(FunctionalTester $I)
    {
    }

    public function _after(FunctionalTester $I)
    {
    }

    public function offerCreate(FunctionalTester $I, \Codeception\Scenario $scenario)
    {
        $I = new FunctionalTester\AdminSteps($scenario);
        $this->initStep($I);
        $I->amOnPage('http://dev.flipit.com/admin/offer/addoffer');
        $this->fillForm($I);
        $I->canSee('De actie is succesvol toegevoegd!');
    }

    protected function initStep($I)
    {
        $I->login();
        $I->canSee('Acties');
        $I->click('Acties');
        $I->amOnPage('http://dev.flipit.com/admin/offer');
        $I->canSee('Add New Offer');
        $I->click('Add New Offer');
    }

    protected function fillForm($I)
    {
        $I->fillField('#selctedshop', 343);
        $I->fillField('#addofferTitle', 'functional test');
        $I->fillField('#couponCodeTxt', 'FUN234');
        $I->fillField('#termsAndcondition', 'TERMS AND CONDITION');
        $I->fillField('#offerImageSelect', 2);
        $I->click('#optionsOnbtn');
        $I->fillField('#offerPosition', 2);
        $I->click('#exclusivebtn');
        $I->click('#addOfferBtn');
    }
}
