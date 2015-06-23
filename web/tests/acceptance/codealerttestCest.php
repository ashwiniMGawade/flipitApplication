<?php
use \AcceptanceTester;

class codealerttestCest
{
    public function _before()
    {
    }

    public function _after()
    {
    }

    public function codealertTest(AcceptanceTester $I, \Codeception\Scenario $scenario)
    {
        $I = new AcceptanceTester\LoginSteps($scenario);
        $I->login();
        $I->canSee('Code alert');
        $I->click('Code alert');
        $I->amOnPage('/admin/email/code-alert');
        $I->canSee('Code alert settings');
        $I->click('Code alert settings');
        $I->amOnPage('/admin/email/code-alert-settings');
        $I->fillField('#emailSubject', 'email subject');
        $I->fillField('#emailHeader', 'email header');
        $I->click('button[type=submit]');
        $I->amOnPage('/admin/email/code-alert-settings');
        $I->seeInField('#emailSubject', 'email subject');
        $I->seeInField('#emailHeader', 'email header');
    }

    public function codealertofferTest(AcceptanceTester $I, \Codeception\Scenario $scenario)
    {
        $I = new AcceptanceTester\LoginSteps($scenario);
        $I->login();
        $this->createShop($I);
        $this->createOffer($I, 'CD', 'couponCode', '2', 'coupon code offer');
        $this->unlinkFilesFromTmp();
        $I->click('li a.menu-icon-offer');
        $I->amOnPage('/admin/offer');
        $I->wait(10);
        $I->click('#offerListTable a');
        $I->amOnPage('/admin/offer/editoffer/id/1');
        $I->click('Send code alert');
        $I->wait(10);
        $I->canSeeInPageSource('modal-body');
    }

    protected function unlinkFilesFromTmp()
    {
        array_map('unlink', glob(dirname(dirname(dirname(__FILE__)))."/public/tmp/*"));
    }

    protected function createOffer($I, $codeType, $codeTilesType, $discountvalueType, $title)
    {
        $I->haveInDatabase(
            'offer_tiles',
            array(
                'label' => 'test',
                'type' => $codeTilesType,
                'ext' => 'png',
                'path' => 'images/upload/offertiles',
                'name' => 'test.png'
            )
        );

        $endDate = $title == 'expired offer' ? date('Y-m-d H:i:s', time() + (60 * 60 * 24 * -7)):
            date('Y-m-d H:i:s', time() + (60 * 60 * 24 * +7));

        $I->haveInDatabase(
            'offer',
            array(
                'shopid' => '1',
                'couponcode' => 'test',
                'tilesId' => '1',
                'title' => $title,
                'created_at' => date('Y-m-d H:i:s', time() + (60 * 60 * 24 * -7)),
                'updated_at' => date('Y-m-d H:i:s', time() + (60 * 60 * 24 * -7)),
                'visability' => 'DE',
                'discounttype' => $codeType,
                'startdate' => date('Y-m-d H:i:s', time() + (60 * 60 * 24 * -7)),
                'enddate' => $endDate,
                'authorId' => 1,
                'shopexist' => 1,
                'couponcodetype' => 'GN',
                'discountvalueType' => $discountvalueType
            )
        );
        $I->haveInDatabase(
            'ref_offer_category',
            array(
                'offerid' => '1',
                'categoryid' => '1'
            )
        );
    }

    protected function createShop($I)
    {
        $I->initializeDb('Db', $I->flipitTestUserDb());

        $I->haveInDatabase(
            'user',
            array(
                'firstname' => 'test',
                'lastname' => 'user',
                'email' => 'test@flipit.com',
                'password' => md5('password'),
                'status' => '1',
                'roleid' => '4',
                'slug' => 'test-user'
            )
        );
        
        $I->initializeDb('Db', $I->flipitTestDb());


        $I->haveInDatabase(
            'category',
            array(
                'name' => 'test cat',
                'permalink' => 'test-cat'
            )
        );
        $I->haveInDatabase(
            'image',
            array(
                'ext' => 'jpg',
                'type' => 'HTUB',
                'path' => 'images/upload/shop/',
                'name' => '1409026126_Jellyfish.jpg',
                'deleted' => 0
            )
        );
        $I->haveInDatabase(
            'image',
            array(
                'ext' => 'jpg',
                'type' => 'HTUB',
                'path' => 'images/upload/shop/',
                'name' => '1409026126_Jellyfish.jpg',
                'deleted' => 0
            )
        );
        $I->haveInDatabase(
            'shop',
            array(
                'name' => 'acceptance shop',
                'permalink' => 'acceptance-shop',
                'title' => 'acceptance shop title',
                'subTitle' => 'acceptance shop title',
                'contentmanagerid' => '1',
                'affliateprogram' => 1,
                'refurl' => 'http://www.kortingscode.nl/',
                'actualurl' => 'http://www.kortingscode.nl/',
                'howtouse' => '1',
                'howtoTitle' => 'acceptance shop title',
                'howtoSubtitle' => 'acceptance shop title',
                'howtoMetaTitle' => 'acceptance shop title',
                'howtoMetaDescription' => 'acceptance shop title',
                'howtousesmallimageid' => 1,
                'howtousebigimageid' => 2,
                'status' => 1
            )
        );
        $I->haveInDatabase(
            'ref_shop_category',
            array(
                'shopid' => '1',
                'categoryid' => '1'
            )
        );
        $I->haveInDatabase(
            'route_permalink',
            array(
                'permalink' => 'acceptance-shop',
                'type' => 'SHP',
                'exactlink' => 'store/storedetail/id/1'
            )
        );
        $I->haveInDatabase(
            'route_permalink',
            array(
                'permalink' => 'how-to/acceptance-shop',
                'type' => 'SHP',
                'exactlink' => 'store/howtoguide/shopid/1'
            )
        );
    }
}
