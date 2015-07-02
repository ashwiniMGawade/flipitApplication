<?php
use Codeception\Util\Stub;

class ShopImportExcelTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    public function testUpdateShopInformation()
    {
        $shopName = 'acceptance shop1';
        $shopId = KC\Repository\Shop::checkShop($shopName);
        if (!empty($shopId)) {
            $this->updateShop($shopId);
        }
        $this->tester->assertEquals(10, count($topOffers));
        $this->tester->assertEquals('test offer1', $topOffers[0]['title']);
        $this->tester->assertEquals('acceptance shop1', $topOffers[0]['shopOffers']['name']);
    }

    private function updateShop($shopId)
    {
        $shopData = array(
            'shopName'=>'test123',
            'shopNavigationUrl'=>'test',
            'moneyShop'=>false,
            'shopOnline'=>1,
            'overwriteTitle'=>'asdasd',
            'metaDescription'=>'asdasd',
            'allowUserGeneratedContent'=>0,
            'allowDiscussions'=>0,
            'shopTitle'=>'sasas',
            'shopSubTitle'=>'sdfsdfsdf',
            'shopNotes'=>'sdfsdfsdf',
            'shopRefURL'=>'http://www.google.com',
            'actualURL'=>'http://www.google.com',
            'shopText'=>'sdasdasd',
            'displaySignupOptions'=>0,
            'displaySimilarShops'=>0
        );
        BackEnd_Helper_importShopsExcel::updateShopData($shopId, $shopData);
        return true;
    }
}
