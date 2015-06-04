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
        $this->persistPopularCodes(20);
        $topOffers = $this->getTopOffers();
        $this->tester->assertEquals(10, $topOffers);
    }

    public function testTopOffersWithLessPopularCodes()
    {
        $this->persistPopularCodes(4);
        $topOffers = $this->getTopOffers();
        $this->tester->assertEquals(10, $topOffers);
    }

    private function persistPopularCodes($count)
    {
        $entityManager = \Codeception\Module\Doctrine2::$em;
        for ($i=1; $i <= $count; $i++) {
            $this->tester->persistEntity(
                new KC\Entity\PopularCode(),
                array(
                    'type' => 'MN',
                    'position' => $i,
                    'status' => 1,
                    'popularcode' => $entityManager->find('KC\Entity\Offer', $i),
                    'deleted' => 0,
                    'created_at' => new \DateTime('now'),
                    'updated_at' => new \DateTime('now'),
                )
            );
        }
    }

    private function getTopOffers()
    {
        $offerRepository = new KC\Repository\Offer;
        $offerListing = new Application_Service_Offer_TopOffer($offerRepository, 10);
        $topOffers = $offerListing->execute(10);
        return count($topOffers);
    }
}
