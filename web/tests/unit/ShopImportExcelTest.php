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

        BackEnd_Helper_importShopsExcel::updateShopData($shopId, $shopData);
        return true;
    }
}
