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

    public function createOffer(WebTester $I, \Codeception\Scenario $scenario)
    {
        $I->canSee('Add New Offer');
        $I->click('Add New Offer');
        $I->canSeeInCurrentUrl('offer/addoffer');
        $this->fillForm($I);
    }

    public function editOffer(WebTester $I, \Codeception\Scenario $scenario)
    {
        $I->haveInDatabasePDOSite(
            'offer',
            array(
                'title' => 'TEST OFFER',
                'visability' => 'DE',
                'discounttype' => 'CD',
                'couponcode' => 'TESTOFFER',
                'startdate' => '2015-07-22 14:42:03',
                'enddate' => '2015-07-22 14:42:03',
                'discountvalueType' => '',
                'authorId' => '1',
                'shopid' => '1',
                'maxcode' => '0',
                'deleted' => '0',
                'created_at' => '2015-07-22 14:42:03',
                'updated_at' => '2015-07-22 14:42:03',
                'userGenerated' => '0',
                'approved' => '0',
                'offline' => '0',
                'tilesId' => '1',
                'shopexist' => '1',
                'couponcodetype' => 'GN'
            )
        );

        $I->amOnPage('admin/offer');
        $I->waitForText('TESTOFFER');
        $I->click('TESTOFFER');
        $I->see('Aanbieding aanpassen');
        $I->click('.liimg');
        $I->click('#optionsOnbtn');
        $I->click('Exclusieve code');
        $I->click('#offerEndButtons button#updateOfferBtn');
        $I->wait(10);
        $I->see('De actie is succesvol aangepast!');
    }

    protected function fillForm($I)
    {
        $I->click('Select a Shop');
        $I->canSee('acceptance shop');
        $I->click('li.select2-highlighted');
        $I->waitForElementVisible('#addofferTitle');
        $I->fillField('#addofferTitle', 'functional test');
        $I->fillField('#couponCodeTxt', 'FUN234');
        $I->click('.liimg');
        $I->click('#optionsOnbtn');
        $I->click('Exclusieve code');
        $I->click('#offerEndButtons button#addOfferBtn');
    }
}
