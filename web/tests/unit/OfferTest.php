<?php
use Codeception\Util\Stub;

class OfferTest extends \Codeception\TestCase\Test
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

    public function testCreateOffer()
    {
        $this->persistCreateOffer();
        $this->tester->seeInRepository('\Core\Domain\Entity\Offer', ['title' => 'functional test']);
    }

    private function persistCreateOffer()
    {
        $futureDate = new \DateTime();
        $futureDate->modify('+1 week');
        $futureDate = $futureDate->format('Y-m-d H:i:s');
        $pastDate = new \DateTime();
        $pastDate->modify('-1 week');
        $pastDate = $pastDate->format('Y-m-d H:i:s');
        $entityManager = \Codeception\Module\Doctrine2::$em;
        $obj = new \Core\Domain\Entity\Offer();
        $this->tester->persistEntity(
            $obj,
            array(
                'shopOffers'=> $entityManager->find('\Core\Domain\Entity\Shop', 1),
                'couponCode'=> 'CD',
                'title'=> 'functional test',
                'Visability'=> 'DE',
                'discountType'=> 'CD',
                'startDate'=> new \DateTime($pastDate),
                'endDate'=> new \DateTime($futureDate),
                'authorId'=> 1,
                'shopExist'=> 1,
                'couponCodeType'=> 'GN',
                'discountvalueType'=> 2,
                'maxcode'=> 0,
                'userGenerated'=> 0,
                'approved'=> 0,
                'offline'=> 0,
                'deleted'=> 0,
                'created_at'=> new \DateTime('now'),
                'updated_at'=> new \DateTime('now'),
                'offer_position'=>1
            )
        );
        return $obj->__get('id');
    }

    public function testGetOffer()
    {
        $id = $this->persistCreateOffer();
        $offer = $this->getOffer($id);
        $this->tester->assertEquals('functional test', $offer[0][0]['title']);
        $this->tester->assertEquals('acceptance shop1', $offer[0][0]['shopOffers']['name']);
    }

    private function getOffer($offerId)
    {
        $offer = KC\Repository\Offer::getOfferInfo($offerId);
        return $offer;
    }

    public function testShopOffers()
    {
        $this->persistCreateOffer();
        $offers = $this->getShopOffers(1);
        $this->tester->assertEquals('functional test', $offers[0]['title']);
    }

    private function getShopOffers($shopId)
    {
        $offers = KC\Repository\Offer::getAllOfferOnShop($shopId);
        return $offers;
    }
}
