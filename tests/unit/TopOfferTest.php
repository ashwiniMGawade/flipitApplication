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
        $entityManager = \Codeception\Module\Doctrine2::$em;
        $this->tester->persistEntity(
            new KC\Entity\PopularCode(),
            array(
                'type' => 'MN',
                'position' => 1,
                'status' => 1,
                'popularcode' => $entityManager->find('KC\Entity\Offer', 1),
                'deleted' => 0,
                'created_at' => new \DateTime('now'),
                'updated_at' => new \DateTime('now'),
            )
        );
        $offerRepository = new KC\Repository\Offer;
        $topOffers = $offerRepository->getTopCouponCodes(array(), 20);
        $test = $this->tester->grabFromRepository('KC\Entity\Offer', 'created_at', array('deleted' => 0));
        print_r($test); die('ss');
        //$offerListing = new Application_Service_Offer_TopOffer($offerRepository, 20);
        $this->tester->assertEquals(1, count($topOffers));
    }
}
