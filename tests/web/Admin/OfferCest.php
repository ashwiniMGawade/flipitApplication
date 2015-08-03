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
                'id' => '10',
                'title' => 'My new Offer Code',
                'visability' => 'DE',
                'discounttype' => 'CD',
                'couponcode' => 'OFFERCODE',
                'startdate' => '2015-07-01 00:05:00',
                'enddate' => '2015-07-08 23:59:00',
                'exclusivecode' => '0',
                'editorpicks' => '0',
                'extendedoffer' => '0',
                'extendedtitle' => '',
                'extendedurl' => '',
                'extendedmetadescription' => '',
                'extendedfulldescription' => '',
                'discount' => '0',
                'discountvalueType' => '0',
                'authorId' => '1',
                'authorName' => 'Some Author',
                'shopid' => '1',
                'maxlimit' => '',
                'maxcode' => '0',
                'deleted' => '0',
                'created_at' => '2015-06-30 17:01:34',
                'updated_at' => '2015-06-30 17:01:34',
                'userGenerated' => '0',
                'approved' => '0',
                'offline' => '0',
                'tilesId' => '331',
                'shopexist' => '1',
                'popularityCount' => '0',
                'couponcodetype' => 'GN',
                'extendedoffertitle' => ''
            )
        );

        $I->amOnPage('admin/offer');
        $I->waitForText('OFFERCODE');
        $I->click('OFFERCODE');
        $I->seeInCurrentUrl('admin/offer/editoffer/id/10');
        $I->click('.liimg');
        $I->click('#optionsOnbtn');
        $I->click('Exclusieve code');
        $I->click('#offerEndButtons button#updateOfferBtn');
        $I->seeInCurrentUrl('admin/offer');
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
