<?php
use Codeception\Util\Stub;

class ExtendedNetworkSubIdTest extends \Codeception\TestCase\Test
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

    public function testOfferClickoutUrl()
    {
        $offerId = 1;
        echo $conversionId = $this->getConversionId($offerId, 'offer');die;
        echo $redirectUrl = $this->getClickoutInformation($offerId, 'offer', $conversionId);die;
        $this->tester->assertEquals(3, count($similarOffers));
        $this->tester->assertEquals('test offer2', $similarOffers[0]['title']);
        $this->tester->assertEquals('acceptance shop2', $similarOffers[0]['shopOffers']['name']);
    }

    /*public function testShopClickoutUrl()
    {
        $shopId = 1;
        $conversionId = $this->getConversionId($shopId, 'shop');
        $redirectUrl = $this->getClickoutInformation($shopId, 'shop', , $conversionId);
        $this->tester->assertEquals(5, count($similarOffers));
        $this->tester->assertEquals('test offer2', $similarOffers[0]['title']);
        $this->tester->assertEquals('acceptance shop2', $similarOffers[0]['shopOffers']['name']);
    }*/

    public function getConversionId($offerId, $type)
    {
        $conversionId = \KC\Repository\Conversions::addConversion($offerId, $type);
        return $conversionId;
    }

    public function getClickoutInformation($id, $type, $conversionId)
    {echo $id;die;
        if ($type == 'offer') {
            $clickout = new FrontEnd_Helper_ClickoutFunctions($id, null);
        } else {
            $clickout = new FrontEnd_Helper_ClickoutFunctions(null, $id);
        }
        $redirectUrl = $clickout->getCloakLink($type, $conversionId);
        return $redirectUrl;
    }

}
