<?php
namespace Usecase\System;

use \Core\Domain\Entity\User\SplashImage;
use \Core\Domain\Service\Purifier;
use \Core\Domain\Usecase\System\GetSplashImagesUsecase;
use \Core\Service\Errors;

class GetSplashImagesUsecaseTest extends \Codeception\TestCase\Test
{
    public function testGetSplashImagesUsecaseReturnsZeroWhenSplashImageDoesNotExist()
    {
        $expectedSplashImages = 0;
        $splashImageRepository = $this->createSplashImagesRepositoryWithFindByMethodMock($expectedSplashImages);
        $splashImages = (new GetSplashImagesUsecase($splashImageRepository, new Purifier(), new Errors()))->execute();
        $this->assertEquals($expectedSplashImages, $splashImages);
    }

    public function testGetSplashImagesUsecaseReturnsArrayWhenRecordExist()
    {
        $splashImage = new SplashImage();
        $expectedResult = array($splashImage);
        $splashImageRepository = $this->createSplashImagesRepositoryWithFindByMethodMock($expectedResult);
        $splashImages = (new GetSplashImagesUsecase($splashImageRepository, new Purifier(), new Errors()))->execute();
        $this->assertEquals(count($expectedResult), count($splashImages));
    }

    public function testGetSplashImagesUsecaseReturnsErrorWhenParametersAreInvalid()
    {
        $conditions = 'invalid';
        $splashImageRepository = $this->createSplashImageRepositoryMock();
        $errors = new Errors();
        $errors->setError('Invalid input, unable to find record.');
        $result = (new GetSplashImagesUsecase($splashImageRepository, new Purifier(), new Errors()))->execute($conditions);
        $this->assertInstanceOf('\Core\Service\Errors', $result);
        $this->assertEquals($errors->getErrorsAll(), $result->getErrorsAll());
    }

    private function createSplashImageRepositoryMock()
    {
        $splashImageRepository = $this->getMock('\Core\Domain\Repository\SplashImageRepositoryInterface');
        return $splashImageRepository;
    }

    private function createSplashImagesRepositoryWithFindByMethodMock($returns)
    {
        $splashImageRepository = $this->createSplashImageRepositoryMock();
        $splashImageRepository->expects($this->once())
            ->method('findBy')
            ->with($this->equalTo('\Core\Domain\Entity\User\SplashImage'), $this->isType('array'))
            ->willReturn($returns);
        return $splashImageRepository;
    }
}
