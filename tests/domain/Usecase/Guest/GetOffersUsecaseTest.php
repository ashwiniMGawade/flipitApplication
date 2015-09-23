<?php
namespace Usecase\Guest;

use \Core\Domain\Entity\Offer;
use \Core\Domain\Service\Purifier;
use \Core\Domain\Usecase\Guest\GetOffersUsecase;
use \Core\Service\Errors;

class GetOffersUsecaseTest extends \Codeception\TestCase\Test
{
    public function testGetOffersUsecaseReturnsZeroWhenOfferDoesNotExist()
    {
        $expectedOffers = 0;
        $offerRepository = $this->createOfferRepositoryWithFindByMethodMock($expectedOffers);
        $offers = (new GetOffersUsecase($offerRepository, new Purifier(), new Errors()))->execute();
        $this->assertEquals($expectedOffers, $offers);
    }

    public function testGetOffersUsecaseReturnsArrayWhenOffersExist()
    {
        $offer = new Offer();
        $expectedResult = array($offer);
        $offerRepository = $this->createOfferRepositoryWithFindByMethodMock($expectedResult);
        $offers = (new GetOffersUsecase($offerRepository, new Purifier(), new Errors()))->execute();
        $this->assertEquals(count($expectedResult), count($offers));
    }

    public function testGetOffersUsecaseReturnsErrorWhenParametersAreInvalid()
    {
        $conditions = 'invalid';
        $offerRepository = $this->createOfferRepositoryMock();
        $errors = new Errors();
        $errors->setError('Invalid input, unable to find offers.');
        $result = (new GetOffersUsecase($offerRepository, new Purifier(), new Errors()))->execute($conditions);
        $this->assertInstanceOf('\Core\Service\Errors', $result);
        $this->assertEquals($errors->getErrorsAll(), $result->getErrorsAll());
    }

    private function createOfferRepositoryMock()
    {
        $offerRepository = $this->getMock('\Core\Domain\Repository\OfferRepositoryInterface');
        return $offerRepository;
    }

    private function createOfferRepositoryWithFindByMethodMock($returns)
    {
        $offerRepository = $this->createOfferRepositoryMock();
        $offerRepository->expects($this->once())
            ->method('findBy')
            ->with($this->equalTo('\Core\Domain\Entity\Offer'), $this->isType('array'))
            ->willReturn($returns);
        return $offerRepository;
    }
}
