<?php
namespace Admin;

use \WebTester;

class FloatingCouponDisplayCest
{
    public function testFloatingCouponDefaultShouldDisplay(WebTester $I, \Codeception\Scenario $scenario)
    {
        $startDate = new \DateTime();
        $endDate = new \DateTime();
        $endDate->add(new \DateInterval('P2D'));
        $I->wantTo('Test floating coupon should not display on shoppage when setting doesn\'t changed .');
        $this->seedOfferData($I, 10, 'Amazing Offer', 'ABCD', $shopId = 1, $startDate, $endDate);
        $I->amOnPage('/acceptance-shop');
        $I->wait(3);
        $I->cantSee('Click here for the coupon code');
    }

    public function testFloatingCouponShouldDisplayAndHideWhenSettingIsOnAndOff(WebTester $I, \Codeception\Scenario $scenario)
    {
        $I = new WebTester\AdminSteps($scenario);
        $I->login();
        $startDate = new \DateTime();
        $endDate = new \DateTime();
        $endDate->add(new \DateInterval('P2D'));
        $I->wantTo('Test floating coupon should display and hide when setting is on and off.');
        $this->seedOfferData($I, 10, 'Amazing Floating Offer', 'ABCD', $shopId = 1, $startDate, $endDate);

        //Turn on the setting and check
        $I->amOnPage('/admin/locale/locale-settings');
        $I->wait(1);
        $I->click('#SHOW_FLOATING_COUPON_ON_BTN');
        $I->click('#save-locale-settings');
        $I->canSee('Locale settings has been updated successfully');
        $I->amOnPage('/acceptance-shop');
        $I->wait(3);
        $I->canSee('Click here for the coupon code');
        //Turn off the setting and check
        $I->amOnPage('/admin/locale/locale-settings');
        $I->wait(1);
        $I->click('#SHOW_FLOATING_COUPON_OFF_BTN');
        $I->click('#save-locale-settings');
        $I->canSee('Locale settings has been updated successfully');
        $I->amOnPage('/acceptance-shop');
        $I->wait(3);
        $I->cantSee('Click here for the coupon code');
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
