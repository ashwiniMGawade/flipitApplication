<?php
namespace Usecase\Admin;

use \Core\Domain\Entity\User\SplashImage;
use \Core\Domain\Service\Purifier;
use \Core\Domain\Usecase\Admin\GetSplashImageUsecase;
use \Core\Service\Errors;

class GetSplashImageUsecaseTest extends \Codeception\TestCase\Test
{
    public function testGetSplashImageUsecaseReturnsErrorWhenRecordDoesNotExist()
    {
        $condition = array('id' => 0);
        $splashImageRepositoryMock = $this->createSplashImageRepositoryWithFindMethodMock($condition, 0);
        $splashImageUsecase = new GetSplashImageUsecase($splashImageRepositoryMock, new Purifier(), new Errors());
        $result = $splashImageUsecase->execute($condition);
        $errors = new Errors();
        $errors->setError('Splash image not found');
        $this->assertEquals($errors->getErrorsAll(), $result->getErrorsAll());
    }

    public function testGetSplashImageUsecaseRetunsObjectWhenValidInputPassed()
    {
        $condition = array('id' => 0);
        $splashImage = new SplashImage();
        $splashImage->setId(0);
        $splashImageRepositoryMock = $this->createSplashImageRepositoryWithFindMethodMock($condition, $splashImage);
        $splashImageUsecase = new GetSplashImageUsecase($splashImageRepositoryMock, new Purifier(), new Errors());
        $result = $splashImageUsecase->execute($condition);
        $this->assertEquals($splashImage, $result);
    }

    public function testGetSplashImageUsecaseReturnsErrorWhenInvalidInputPassed()
    {
        $condition = 'invalid';
        $splashImageRepositoryMock = $this->createSplashImageRepositoryMock();
        $splashImageUsecase = new GetSplashImageUsecase($splashImageRepositoryMock, new Purifier(), new Errors());
        $result = $splashImageUsecase->execute($condition);
        $errors = new Errors();
        $errors->setError('Invalid input, unable to find image.');
        $this->assertEquals($errors->getErrorsAll(), $result->getErrorsAll());
    }

    private function createSplashImageRepositoryMock()
    {
        $splashImageRepository = $this->getMockBuilder('\Core\Domain\Repository\SplashImageRepositoryInterface')->getMock();
        return $splashImageRepository;
    }

    private function createSplashImageRepositoryWithFindMethodMock($condition, $returns)
    {
        $splashImageRepositoryMock = $this->createSplashImageRepositoryMock();
        $splashImageRepositoryMock
            ->expects($this->once())
            ->method('findOneBy')
            ->with($this->equalTo('\Core\Domain\Entity\User\SplashImage'), $this->equalTo($condition))
            ->willReturn($returns);
        return $splashImageRepositoryMock;
    }
}
