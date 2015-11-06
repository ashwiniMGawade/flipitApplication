<?php
namespace Usecase\System;

use \Core\Domain\Entity\User\SplashPage;
use \Core\Domain\Service\Purifier;
use \Core\Domain\Usecase\System\GetSplashPageUsecase;
use \Core\Service\Errors;

class GetSplashPageUsecaseTest extends \Codeception\TestCase\Test
{
    public function testGetSplashPageUsecaseReturnsErrorWhenRecordDoestNotExist()
    {
        $condition = array('id' => 0);
        $splashPageRepositoryMock = $this->createSplashPageRepositoryWithFindMethodMock($condition, 0);
        $splashPageUsecase = new GetSplashPageUsecase($splashPageRepositoryMock, new Purifier(), new Errors());
        $result = $splashPageUsecase->execute($condition);
        $errors = new Errors();
        $errors->setError('Splash page not found');
        $this->assertEquals($errors->getErrorsAll(), $result->getErrorsAll());
    }

    public function testGetSplashPageUsecaseReturnsObjectWhenValidInputPassed()
    {
        $condition = array('id' => 0);
        $splashPage = new SplashPage();
        $splashPage->setId(0);
        $splashPageRepositoryMock = $this->createSplashPageRepositoryWithFindMethodMock($condition, $splashPage);
        $splashPageUsecase = new GetSplashPageUsecase($splashPageRepositoryMock, new Purifier(), new Errors());
        $result = $splashPageUsecase->execute($condition);
        $this->assertEquals($splashPage, $result);
    }

    public function testGetSplashPageUsecaseReturnsErrorWhenInvalidInputPassed()
    {
        $condition = 'invalid';
        $splashPageRepositoryMock = $this->createSplashPagesRepositoryMock();
        $splashPageUsecase = new GetSplashPageUsecase($splashPageRepositoryMock, new Purifier(), new Errors());
        $result = $splashPageUsecase->execute($condition);
        $errors = new Errors();
        $errors->setError('Invalid input, unable to find splash page.');
        $this->assertEquals($errors->getErrorsAll(), $result->getErrorsAll());
    }

    private function createSplashPagesRepositoryMock()
    {
        $splashPageRepository = $this->getMockBuilder('\Core\Domain\Repository\SplashPageRepositoryInterface')->getMock();
        return $splashPageRepository;
    }

    private function createSplashPageRepositoryWithFindMethodMock($condition, $returns)
    {
        $splashPageRepositoryMock = $this->createSplashPagesRepositoryMock();
        $splashPageRepositoryMock
            ->expects($this->once())
            ->method('findOneBy')
            ->with($this->equalTo('\Core\Domain\Entity\User\SplashPage'), $this->equalTo($condition))
            ->willReturn($returns);
        return $splashPageRepositoryMock;
    }
}
