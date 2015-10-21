<?php
namespace Usecase\System;

use \Core\Domain\Entity\User\Splash;
use \Core\Domain\Service\Purifier;
use \Core\Domain\Usecase\System\GetSplashOffersUsecase;
use \Core\Service\Errors;

class GetSplashOffersUsecaseTest extends \Codeception\TestCase\Test
{
    public function testGetSplashOffersUsecaseReturnsZeroWhenSplashOfferDoesNotExist()
    {
        $expectedSplashOffers = 0;
        $splashOfferRepository = $this->createSplashOffersRepositoryWithFindByMethodMock($expectedSplashOffers);
        $splashOffers = (new GetSplashOffersUsecase($splashOfferRepository, new Purifier(), new Errors()))->execute();
        $this->assertEquals($expectedSplashOffers, $splashOffers);
    }

    public function testGetSplashOffersUsecaseReturnsArrayWhenRecordExist()
    {
        $splashOffer = new Splash();
        $expectedResult = array($splashOffer);
        $splashOfferRepository = $this->createSplashOffersRepositoryWithFindByMethodMock($expectedResult);
        $splashOffers = (new GetSplashOffersUsecase($splashOfferRepository, new Purifier(), new Errors()))->execute();
        $this->assertEquals(count($expectedResult), count($splashOffers));
    }

    public function testGetSplashOffersUsecaseReturnsErrorWhenParametersAreInvalid()
    {
        $conditions = 'invalid';
        $splashOfferRepository = $this->createSplashOfferRepositoryMock();
        $errors = new Errors();
        $errors->setError('Invalid input, unable to find record.');
        $result = (new GetSplashOffersUsecase($splashOfferRepository, new Purifier(), new Errors()))->execute($conditions);
        $this->assertInstanceOf('\Core\Service\Errors', $result);
        $this->assertEquals($errors->getErrorsAll(), $result->getErrorsAll());
    }

    private function createSplashOfferRepositoryMock()
    {
        $splashOfferRepository = $this->getMock('\Core\Domain\Repository\SplashOfferRepositoryInterface');
        return $splashOfferRepository;
    }

    private function createSplashOffersRepositoryWithFindByMethodMock($returns)
    {
        $splashOfferRepository = $this->createSplashOfferRepositoryMock();
        $splashOfferRepository->expects($this->once())
            ->method('findBy')
            ->with($this->equalTo('\Core\Domain\Entity\User\Splash'), $this->isType('array'))
            ->willReturn($returns);
        return $splashOfferRepository;
    }
}
