<?php
namespace Usecase\Guest;

use Core\Domain\Entity\Offer;
use Core\Domain\Service\Purifier;
use Core\Domain\Usecase\Guest\GetOfferUsecase;
use Core\Service\Errors;

class GetOfferUsecaseTest extends \Codeception\TestCase\Test
{
    /**
     * @var \DomainTester
     */
    protected $tester;

    public function testGetOfferUsecaseReturnsAnObjectOfOffers()
    {
        $conditions['id'] = 1;
        $offer = new Offer();
        $offer->__set('id', $conditions['id']);
        $offerRepository = $this->createOfferRepositoryWithFindOneByMethodMock($offer);
        $result = (new GetOfferUsecase($offerRepository, new Purifier(), new Errors()))->execute($conditions);
        $this->assertInstanceOf('\Core\Domain\Entity\Offer', $result);
        $this->assertEquals($result->getId(), $conditions['id']);
    }

    public function testGetOfferUsecaseReturnsErrorWhenParamsIsNotArray()
    {
        $conditions = 1;
        $offerRepository = $this->createOfferRepositoryMock();
        $errors = new Errors();
        $errors->setError('Invalid input, unable to find offer.');
        $result = (new GetOfferUsecase($offerRepository, new Purifier(), new Errors()))->execute($conditions);
        $this->assertInstanceOf('\Core\Service\Errors', $result);
        $this->assertEquals($errors->getErrorsAll(), $result->getErrorsAll());
    }

    public function testGetOfferUsecaseWhenIdDoesNotExist()
    {
        $conditions['id'] = 0;
        $offerRepository = $this->createOfferRepositoryWithFindOneByMethodMock(false);
        $result = (new GetOfferUsecase($offerRepository, new Purifier(), new Errors()))->execute($conditions);
        $errors = new Errors();
        $errors->setError('Offer not found');
        $this->assertEquals($errors->getErrorsAll(), $result->getErrorsAll());
    }

    private function createOfferRepositoryMock()
    {
        $offerRepository = $this->getMockBuilder('\Core\Domain\Repository\OfferRepositoryInterface')->getMock();
        return $offerRepository;
    }

    private function createOfferRepositoryWithFindOneByMethodMock($returns)
    {
        $offerRepository = $this->createOfferRepositoryMock();
        $offerRepository->expects($this->once())
                       ->method('findOneBy')
                       ->with($this->equalTo('\Core\Domain\Entity\Offer'), $this->isType('array'))
                       ->willReturn($returns);
        return $offerRepository;
    }
}
