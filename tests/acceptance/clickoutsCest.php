<?php
use \AcceptanceTester;

class clickoutsCest
{
    public function _before()
    {
    }

    public function _after()
    {
    }

    public function sidebarClickout(AcceptanceTester $I, \Codeception\Scenario $scenario)
    {
        $this->unlinkFilesFromTmp();
        $I = new AcceptanceTester($scenario);
        $this->createShop($I);
        $this->commonClickouts($I, '.web a');
    }

    public function headerLinkClickout(AcceptanceTester $I, \Codeception\Scenario $scenario)
    {
        $this->unlinkFilesFromTmp();
        $I = new AcceptanceTester($scenario);
        $this->createShop($I);
        $this->commonClickouts($I, '.header-block-2 .box');
    }

    public function headerImageClickout(AcceptanceTester $I, \Codeception\Scenario $scenario)
    {
        $this->unlinkFilesFromTmp();
        $I->wait(10);
        $I = new AcceptanceTester($scenario);
        $this->createShop($I);
        $this->commonClickouts($I, '.icon a');
    }
    public function couponCodeClickout(AcceptanceTester $I, \Codeception\Scenario $scenario)
    {
        $this->unlinkFilesFromTmp();
        $I = new AcceptanceTester($scenario);
        $this->createShop($I);
        $this->createOffer($I, 'CD', 'couponCode', '2', 'coupon code offer');
        $this->switchOfferClickouts('couponCode', '.buttons a:first-child', '', $I);
    }
    
    public function saleClickout(AcceptanceTester $I, \Codeception\Scenario $scenario)
    {
        $this->unlinkFilesFromTmp();
        $I = new AcceptanceTester($scenario);
        $this->createShop($I);
        $this->createOffer($I, 'SL', 'sale', '1', 'sale offer');
        $this->switchOfferClickouts('sale', '.buttons a:first-child', '.clickout-title a', $I);
    }
 
    public function expiredClickout(AcceptanceTester $I, \Codeception\Scenario $scenario)
    {
        $this->unlinkFilesFromTmp();
        $I = new AcceptanceTester($scenario);
        $this->createShop($I);
        $this->createOffer($I, 'CD', 'couponCode', '2', 'expired offer');
        $this->switchOfferClickouts('expired', '', '.line a', $I);
    }

    public function printableClickout(AcceptanceTester $I, \Codeception\Scenario $scenario)
    {
        $this->unlinkFilesFromTmp();
        $I = new AcceptanceTester($scenario);
        $this->createShop($I);
        $this->createOffer($I, 'PA', 'printable', '0', 'printable offer');
        $this->switchOfferClickouts('printable', '.buttons a:first-child', '.clickout-title a', $I);
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

    protected function commonClickouts($I, $cssClassName)
    {
        $I->amOnPage('/acceptance-shop');
        $I->click($cssClassName);
        $I->switchToWindow();
        $I->seeInCurrentUrl('/');
    }

    protected function switchOfferClickouts($codeType, $tagName, $cssClassName, $I)
    {
        switch ($codeType) {
            case 'couponCode':
                $this->commonOfferClickouts($tagName, $I);
                $I->canSeeInPageSource('id="code-lightbox"');
                $I->canSeeInPageSource('id="code-button"');
                break;
            case 'sale':
                $this->commonOfferClickouts($tagName, $I);
                $I->seeInCurrentUrl('/');
                $I->wait(5);
                $I->amOnPage('/acceptance-shop');
                $I->click($cssClassName);
                $I->wait(5);
                $I->seeInCurrentUrl('/');
                $I->wait(5);
                break;
            case 'printable':
                $I->amOnPage('/acceptance-shop');
                $I->click($tagName);
                $I->seeInCurrentUrl('/');
                $I->wait(5);
                $I->amOnPage('/acceptance-shop');
                $I->click($cssClassName);
                $I->wait(5);
                $I->seeInCurrentUrl('/');
                $I->wait(5);
                break;
            case 'expired':
                $I->amOnPage('/acceptance-shop');
                $I->click($cssClassName);
                $I->switchToWindow();
                break;
            default:
                break;
        }
    }

    protected function commonOfferClickouts($tagName, $I)
    {
        $I->amOnPage('/acceptance-shop');
        $I->wait(5);
        $I->click($tagName);
        $I->executeInSelenium(function (\Webdriver $webdriver) {
            $handles=$webdriver->getWindowHandles();
            $last_window = end($handles);
            $webdriver->switchTo()->window($last_window);
        });
        $I->wait(10);
    }

    protected function unlinkFilesFromTmp()
    {
        array_map('unlink', glob(dirname(dirname(dirname(__FILE__)))."/public/tmp/*"));
    }
}
