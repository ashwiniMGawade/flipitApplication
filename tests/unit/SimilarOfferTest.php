<?php
use Codeception\Util\Stub;

class SimilarOfferTest extends \Codeception\TestCase\Test
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

    public function testSimilarOffers()
    {
        $this->persistRefShopRelatedshop(5);
        $similarOffers = $this->getSimilarOffers(1, 1);
        $this->tester->assertEquals(3, count($similarOffers));
        $this->tester->assertEquals('test offer2', $similarOffers[0]['title']);
        $this->tester->assertEquals('acceptance shop2', $similarOffers[0]['shopOffers']['name']);
    }

    public function testSimilarOffersForNoMoney()
    {
        $this->persistRefShopRelatedshop(5);
        $similarOffers = $this->getSimilarOffers(1, 0);
        $this->tester->assertEquals(5, count($similarOffers));
        $this->tester->assertEquals('test offer2', $similarOffers[0]['title']);
        $this->tester->assertEquals('acceptance shop2', $similarOffers[0]['shopOffers']['name']);
    }

    public function testNoSimilarOffers()
    {
        $this->persistRefShopRelatedshop(5);
        $similarOffers = $this->getSimilarOffers(100, 0);
        $this->tester->assertEquals('', '');
    }

    private function persistRefShopRelatedshop($count)
    {
        $entityManager = \Codeception\Module\Doctrine2::$em;
        for ($i=1; $i <= $count; $i++) {
            $this->tester->persistEntity(
                new \Core\Domain\Entity\RefShopRelatedshop(),
                array(
                    'relatedshopId' => $i + 1,
                    'position' => $i,
                    'shop' => $entityManager->find('\Core\Domain\Entity\Shop', 1),
                    'created_at' => new \DateTime('now'),
                    'updated_at' => new \DateTime('now'),
                )
            );
        }
    }

    private function getSimilarOffers($shopId, $affiliateProgram)
    {
        $offerRepository = new KC\Repository\Offer;
        $offerListing = new Application_Service_Offer_SimilarOffer($offerRepository, $shopId, $affiliateProgram);
        $similarOffers = $offerListing->fetchSimilarShopOffers();
        return $similarOffers;
    }
}
