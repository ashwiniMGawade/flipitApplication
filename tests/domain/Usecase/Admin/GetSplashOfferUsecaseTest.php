<?php
namespace Usecase\Admin;

use \Core\Domain\Entity\User\Splash;
use \Core\Domain\Service\Purifier;
use \Core\Domain\Usecase\Admin\GetSplashOfferUsecase;
use \Core\Service\Errors;

class GetSplashOfferUsecaseTest extends \Codeception\TestCase\Test
{
    public function testGetSplashOfferUsecaseReturnsErrorWhenRecordDoesNotExist()
    {
        $condition = array('id' => 0);
        $splashOfferRepositoryMock = $this->createSplashOfferRepositoryWithFindMethodMock($condition, 0);
        $splashOfferUsecase = new GetSplashOfferUsecase($splashOfferRepositoryMock, new Purifier(), new Errors());
        $result = $splashOfferUsecase->execute($condition);
        $errors = new Errors();
        $errors->setError('Record not found');
        $this->assertEquals($errors->getErrorsAll(), $result->getErrorsAll());
    }

    public function testGetSplashOfferUsecaseReturnsDataWhenRecordExists()
    {
        $condition = array('id' => 1);
        $splashOffer = new Splash();
        $splashOffer->setId(1);
        $splashOfferRepositoryMock = $this->createSplashOfferRepositoryWithFindMethodMock($condition, $splashOffer);
        $splashOfferUsecase = new GetSplashOfferUsecase($splashOfferRepositoryMock, new Purifier(), new Errors());
        $result = $splashOfferUsecase->execute($condition);
        $this->assertEquals($splashOffer, $result);
    }

    public function testGetSplashOfferUsecaseReturnsErrorWhenConditionIsInvalid()
    {
        $condition = 'invalid';
        $splashOfferRepositoryMock = $this->createSplashOfferRepositoryMock();
        $splashOfferUsecase = new GetSplashOfferUsecase($splashOfferRepositoryMock, new Purifier(), new Errors());
        $result = $splashOfferUsecase->execute($condition);
        $errors = new Errors();
        $errors->setError('Invalid input, unable to find record.');
        $this->assertEquals($errors->getErrorsAll(), $result->getErrorsAll());
    }

    private function createSplashOfferRepositoryMock()
    {
        $splashOfferRepository = $this->getMockBuilder('\Core\Domain\Repository\SplashOfferRepositoryInterface')->getMock();
        return $splashOfferRepository;
    }

    private function createSplashOfferRepositoryWithFindMethodMock($condition, $returns)
    {
        $splashOfferRepositoryMock = $this->createSplashOfferRepositoryMock();
        $splashOfferRepositoryMock
            ->expects($this->once())
            ->method('findOneBy')
            ->with($this->equalTo('\Core\Domain\Entity\User\Splash'), $this->equalTo($condition))
            ->willReturn($returns);
        return $splashOfferRepositoryMock;
    }
}
