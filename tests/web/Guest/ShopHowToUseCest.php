<?php
namespace Guest;
use \WebTester;

class ShopHowToUseCest
{
    public function testAccessingHowToPageOfAShopGivesA404ErrorWhenItIsNotEnabled(WebTester $I, \Codeception\Scenario $scenario)
    {
        $I->wantTo('Test Accessing How To Page of a shop gives a 404 error when It is not enabled.');

        $I->haveInDatabasePDOSite(
            'shop',
            array(
                'id' => '100',
                'name' => 'Test Shop',
                'permalink' => 'test-shop',
                'metadescription' => 'Test meta desc ',
                'usergenratedcontent' => '0',
                'notes' => 'Uggs, Nike, Home, tassen, jurken, schoenen, sale',
                'deeplink' => '',
                'deeplinkstatus' => '0',
                'refurl' => 'http://google.com',
                'actualurl' => 'http://www.google.com',
                'affliateprogram' => '1',
                'title' => 'Zalando kortingscodes',
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
                'status' => '1',
                'offlinesicne' => NULL,
                'accoutmanagerid' => '0',
                'accountManagerName' => '',
                'contentmanagerid' => '211',
                'contentManagerName' => 'Myrthe Oyen',
                'logoid' => NULL,
                'screenshotid' => '1',
                'howtousesmallimageid' => NULL,
                'howtousebigimageid' => NULL,
                'affliatenetworkid' => '1',
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

        $I->haveInDatabasePDOSite('ref_shop_category', array('id' => '','shopid' => '100','categoryid' => '1','created_at' => '0000-00-00 00:00:00','updated_at' => '0000-00-00 00:00:00'));

        $I->amOnPage('/how-to/test-shop');
        $I->canSee('Sorry, deze pagina bestaat niet!');
    }
}
