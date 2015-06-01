<?php
use Codeception\Util\Stub;

class TopOfferTest extends \Codeception\TestCase\Test
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

    public function testTopOffers()
    {
        $offerRepositoryMock = $this->createOffersRepository();
        $offerListing = new Application_Service_Offer_TopOffer($offerRepositoryMock, 20);
        $topOffers = $offerListing->execute(20);
        $this->tester->assertEquals(20, count($topOffers));
    }

    private function createOffersRepository()
    {
        $offerRepository = $this->getMock('KC\Repository\Offer');
        //$offerEntity = new KC\Repository\Offer;
        $offerRepository::staticExpects($this->once())
            ->method('getTopCouponCodes')
            ->will($this->returnValue('test'));
        return $offerRepository;
    }
}
