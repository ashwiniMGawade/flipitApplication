<?php
namespace Admin;

use \WebTester;

class SplashPageCest
{
    public function testSplashPageContent(WebTester $I, \Codeception\Scenario $scenario)
    {
        $I = new WebTester\AdminSteps($scenario);
        $I->login();
        $I->wantTo('Test splash page content update.');
        $I->amOnPage('/admin/splash/page');
        $I->wait(3);
        $I->click('#updateFooterButton');
        $I->wait(1);
        $I->canSee('Splash page has been updated successfully');
    }

    public function testSplashPageOffers(WebTester $I, \Codeception\Scenario $scenario)
    {
        $I = new WebTester\AdminSteps($scenario);
        $I->login();
        $startDate = new \DateTime();
        $endDate = new \DateTime();
        $endDate->add(new \DateInterval('P2D'));
        $I->wantTo('Test user can add offers on splash page .');
        $this->seedOfferData($I, 10, 'Offer Special for Splash', 'ABCD', 1, $startDate, $endDate);
        $I->amOnPage('/admin/splash');
        $I->wait(1);
        $I->click('Add New Offer');
        $this->fillAddOfferForm($I);
    }

    /*public function testSplashPageFeaturedImages(WebTester $I, \Codeception\Scenario $scenario)
    {
        $I = new WebTester\AdminSteps($scenario);
        $I->login();
        $I->wantTo('Test user can add upload featured images on splash page .');
        $I->amOnPage('/admin/splash/images');
        $I->wait(1);
        $I->click('Upload New Image');
        $I->wait(1);
        $I->attachFile('#featuredImage', 'Chrysanthemum.jpg');
        $I->wait(1);
        $I->click('#submitUploadBtn');
    }*/

    private function fillAddOfferForm($I)
    {
        $I->wait(1);
        $I->selectOption("#locale", 'kortingscode.nl');
        $I->waitForText('Winkel');
        $I->click('Search Shop');
        $I->fillField('input[type=text]', 'acceptance');
        $I->wait(2);
        $I->click('.select2-highlighted');
        $I->waitForText('Acties');
        $I->click('Search Offer');
        $I->fillField('input.select2-focused[type=text]', 'Offer Special');
        $I->wait(2);
        $I->click('.select2-highlighted');
        $I->click('#updateseenIdButton');
        $I->wait(1);
        $I->canSee('Offer has been added successfully');
    }

    private function seedOfferData($I, $id, $title, $code, $shopId, $startDate, $endDate)
    {
        $I->haveInDatabasePDOSite(
            'offer',
            array(
                'id' => $id,
                'title' => $title,
                'visability' => 'DE',
                'discounttype' => 'CD',
                'couponcode' => $code,
                'startdate' => $startDate->format('Y-m-d H:i:s'),
                'enddate' => $endDate->format('Y-m-d H:i:s'),
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
                'shopid' => $shopId,
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
    }
}
