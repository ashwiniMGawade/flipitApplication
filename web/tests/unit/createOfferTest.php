<?php
use Codeception\Util\Stub;

class createOfferTest extends \Codeception\TestCase\Test
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
        $this->tester->assertEquals('functional test', 'functional test');
    }

    private function persistCreateOffer()
    {
        $entityManager = \Codeception\Module\Doctrine2::$em;
        $this->tester->persistEntity(
            new \Core\Domain\Entity\Offer(),
            array(
                'shopOffers'=> $this->entityManager->find('\Core\Domain\Entity\Shop', 1),
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
    }
}
