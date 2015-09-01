<?php
namespace Admin;

use \WebTester;

class OffersFilterCest
{
    public function _before(WebTester $I, \Codeception\Scenario $scenario)
    {
        $I = new WebTester\AdminSteps($scenario);
        $I->login();
    }

    public function testShopRatingsFilter(WebTester $I, \Codeception\Scenario $scenario)
    {
        $startDate = new \DateTime();
        $endDate = new \DateTime();;
        $endDate->add(new \DateInterval('P10D'));
        $I->wantTo('Test shops ratings filter on offers listing.');
        $this->seedShopData($I, 100, 'Amazing shop', $permalink = 'test1', $afiliateNetworkId = 2, $status = 1, $rating = 5 );
        $this->seedOfferData($I, 10, 'Amazing Offer for shop rating AAA', 'ABCD', $shopId = 100, $startDate, $endDate);
        $I->amOnPage('/admin/offer');
        $I->wait(1);
        $I->click('All Shop Ratings');
        $I->canSee('AAA');
        $I->click('li.select2-highlighted');
        $I->click('#searchShopeButton');
        $I->wait(1);
        $I->canSee('Amazing Offer for shop rating AAA');
    }

    public function testExpiredFilterWithAlmostExpiredSelected(WebTester $I, \Codeception\Scenario $scenario)
    {
        $startDate = new \DateTime();
        $endDate = new \DateTime();;
        $startDate->sub(new \DateInterval('P2D'));
        $endDate->add(new \DateInterval('P2D'));
        $I->wantTo('Test expired filter with almost expired selected on offers listing.');
        $this->seedOfferData($I, 10, 'Almost Expired Offer', 'ABCD', $shopId = 100, $startDate, $endDate);
        $I->amOnPage('/admin/offer');
        $I->wait(1);
        $I->click('Expired/Not Expired');
        $I->canSee('Almost Expired');
        $I->click('li.select2-highlighted');
        $I->click('#searchShopeButton');
        $I->wait(1);
        $I->canSee('Almost Expired Offer');
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

    private function seedShopData($I, $id, $name, $permalink, $afliateNetworkId, $status, $rating)
    {
        $I->haveInDatabasePDOSite(
            'shop',
            array(
                'id' => $id,
                'name' => $name,
                'permalink' => $permalink,
                'metadescription' => 'Test meta desc ',
                'usergenratedcontent' => '0',
                'notes' => 'Uggs, Nike, Home, tassen, jurken, schoenen, sale',
                'deeplink' => '',
                'deeplinkstatus' => '0',
                'refurl' => 'http://google.com',
                'actualurl' => 'http://www.google.com',
                'affliateprogram' => '1',
                'title' => $name,
                'subTitle' => 'Sub title',
                'overritetitle' => 'over title',
                'overritesubtitle' => 'online',
                'overritebrowsertitle' => 'my shop kortingscode overzicht voor korting op schoenen',
                'shoptext' => 'This shop is een van die webwinkels die precies weet hoe ze kortingscodes moet inzetten. ',
                'views' => '0',
                'howtouse' => '0',
                'Deliverytime' => '1 tot 3 ',
                'returnPolicy' => '30',
                'freeDelivery' => '2',
                'deliveryCost' => ' ',
                'status' => $status,
                'offlinesicne' => NULL,
                'accoutmanagerid' => '0',
                'accountManagerName' => '',
                'contentmanagerid' => '211',
                'contentManagerName' => 'Myrthe Oyen',
                'logoid' => NULL,
                'screenshotid' => '1',
                'howtousesmallimageid' => NULL,
                'howtousebigimageid' => NULL,
                'affliatenetworkid' => $afliateNetworkId,
                'howtousepageid' => '42',
                'keywordlink' => NULL,
                'deleted' => '0',
                'created_at' => '2010-09-21 17:41:07',
                'updated_at' => '2015-06-17 14:34:26',
                'howtoTitle' => 'Hoe wissel je een kortingscode van Zalando in?',
                'howtoSubtitle' => 'Zo wissel je makkelijk een kortingscode van Zalando in',
                'howtoMetaTitle' => 'Hoe voer je een kortingscode van Zalando in? ',
                'howtoMetaDescription' => 'Met de Zalando kortingscodes bespaar je gemakkelijk en snel bij de Zalando shop lees direct hoe je deze kunt invoeren op www.zalando.nl',
                'ideal' => NULL,
                'qShops' => NULL,
                'freeReturns' => NULL,
                'pickupPoints' => NULL,
                'mobileShop' => NULL,
                'service' => NULL,
                'serviceNumber' => NULL,
                'discussions' => '1',
                'displayExtraProperties' => '0',
                'showsignupoption' => '1',
                'addtosearch' => '0',
                'customheader' => '',
                'totalviewcount' => '27845',
                'showSimliarShops' => '1',
                'showchains' => '0',
                'chainItemId' => NULL,
                'chainId' => NULL,
                'strictconfirmation' => '1',
                'howToIntroductionText' => '',
                'brandingcss' => '',
                'lightboxfirsttext' => '',
                'lightboxsecondtext' => '',
                'customtext' => '',
                'showcustomtext' => '0',
                'customtextposition' => NULL,
                'lastSevendayClickouts' => '537',
                'shopAndOfferClickouts' => '27848',
                'shopsViewedIds' => '1379',
                'howtoSubSubTitle' => '',
                'moretextforshop' => '',
                'howtoguideslug' => 'code-inwisselen-tips',
                'futurecode' => '0',
                'code_alert_send_date' => '2015-05-15 00:00:00',
                'featuredtext' => '',
                'featuredtextdate' => NULL,
                'classification' => $rating
            )
        );
    }
}
