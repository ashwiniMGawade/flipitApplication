<?php
namespace Usecase\Guest;


use Core\Domain\Usecase\Guest\GetOfferUsecase;

class GetOfferUsecaseTest extends \Codeception\TestCase\Test
{
    /**
     * @var \DomainTester
     */
    protected $tester;

    public function testGetOfferUsecaseReturnsAnObjectOfOffers()
    {
        $offerId = 1;
        $result = (new GetOfferUsecase())->execute($offerId);
        $this->assertInstanceOf('\Core\Domain\Entity\Offer', $result);
        $this->assertEquals($result->getId(), $offerId);
    }
}
