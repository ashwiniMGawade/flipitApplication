<?php
namespace Guest;
use \WebTester;

class ShopsFilterCest
{
    public function _before(WebTester $I, \Codeception\Scenario $scenario)
    {
        $I = new WebTester\AdminSteps($scenario);
        $I->login();
    }

    public function testAfliateNetworkFilter(WebTester $I, \Codeception\Scenario $scenario)
    {
        $I->wantTo('Test afliate network filter on shoppage.');
        $shop1 = 'My afliate shop';
        $shop2 = 'Normal Shop';
        $this->seedAfliateNetwork($I, 100, 'Acceptance Afliate' );
        $this->seedShopData($I, 100, $shop1, $permalink = 'test1', $afiliateNetworkId = 100, $status = 1 );
        $this->seedShopData($I, 101, $shop2, $permalink = 'test1', $afiliateNetworkId = 1, $status = 1 );

        $I->amOnPage('/admin/shop');

        $I->wait(1);
        $I->canSee($shop1);
        $I->canSee($shop2);
        $I->selectOption('#affliatenetworkid', 'Acceptance Afliate');
        $I->click('#searchByShop');
        $I->wait(1);
        $I->canSee($shop1);
        $I->cantSee($shop2);
    }

    public function testOnlineOrOfflineShopFilter(WebTester $I, \Codeception\Scenario $scenario)
    {
        $I->wantTo('Test online/offline filter on shoppage.');
        $shop1 = 'My online shop';
        $shop2 = 'My Offline shop';
        $this->seedShopData($I, 100, $shop1, $permalink = 'test1', $afiliateNetworkId = 2, $status = 1 );
        $this->seedShopData($I, 101, $shop2, $permalink = 'test1', $afiliateNetworkId = 1, $status = 0 );

        $I->amOnPage('/admin/shop');

        $I->wait(1);
        $I->canSee($shop1);
        $I->canSee($shop2);
        $I->selectOption('#shop_status', 'Online');
        $I->click('#searchByShop');
        $I->wait(1);
        $I->canSee($shop1);
        $I->cantSee($shop2);
        $I->selectOption('#shop_status', 'Offline');
        $I->click('#searchByShop');
        $I->wait(1);
        $I->cantSee($shop1);
        $I->canSee($shop2);
    }

    private function seedAfliateNetwork($I, $id, $name)
    {
        $I->haveInDatabasePDOSite(
            'affliate_network',
            array(
                'id'            => $id,
                'name'          => $name,
                'status'        => 1,
                'deleted'       => 0,
                'created_at'    => '2015-05-15 00:00:00',
                'updated_at'    => '2015-05-15 00:00:00'
            )
        );
    }

    private function seedShopData($I, $id, $name, $permalink, $afliateNetworkId, $status)
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
                'featuredtextdate' => NULL
            )
        );
    }
}
