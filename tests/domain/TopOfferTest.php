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

    // public function testTopOffers()
    // {
    //     $this->persistPopularCodes(20);
    //     $topOffers = $this->getTopOffers();
    //     $this->tester->assertEquals(10, count($topOffers));
    //     $this->tester->assertEquals('test offer1', $topOffers[0]['title']);
    //     $this->tester->assertEquals('acceptance shop1', $topOffers[0]['shopOffers']['name']);
    // }

    // public function testTopOffersWithLessPopularCodes()
    // {
    //     $this->persistPopularCodes(4);
    //     $topOffers = $this->getTopOffers();
    //     $this->tester->assertEquals(10, count($topOffers));
    //     $this->tester->assertEquals('test offer1', $topOffers[0]['title']);
    //     $this->tester->assertEquals('acceptance shop1', $topOffers[0]['shopOffers']['name']);
    // }

    // private function persistPopularCodes($count)
    // {
    //     $entityManager = \Codeception\Module\Doctrine2::$em;
    //     for ($i=1; $i <= $count; $i++) {
    //         $this->tester->persistEntity(
    //             new \Core\Domain\Entity\PopularCode(),
    //             array(
    //                 'type' => 'MN',
    //                 'position' => $i,
    //                 'status' => 1,
    //                 'popularcode' => $entityManager->find('\Core\Domain\Entity\Offer', $i),
    //                 'deleted' => 0,
    //                 'created_at' => new \DateTime('now'),
    //                 'updated_at' => new \DateTime('now'),
    //             )
    //         );
    //     }
    // }

    // private function getTopOffers()
    // {
    //     $offerRepository = new KC\Repository\Offer;
    //     $offerListing = new Application_Service_Offer_TopOffer($offerRepository, 10);
    //     $topOffers = $offerListing->execute(10);
    //     return $topOffers;
    // }
}
