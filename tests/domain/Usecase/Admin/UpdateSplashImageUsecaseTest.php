<?php
namespace Usecase\Admin;

use \Core\Domain\Entity\User\SplashImage;
use \Core\Domain\Service\Purifier;
use Core\Domain\Service\Validator;
use \Core\Domain\Usecase\Admin\UpdateSplashImageUsecase;
use \Core\Domain\Validator\SplashImageValidator;
use \Core\Service\Errors;

class UpdateSplashImageUsecaseTest extends \Codeception\TestCase\Test
{
    public function testUpdateSplashImageUsecaseReturnsErrorsObjectWithInvalidParameters()
    {
        $params = array(
            'image' => null
        );
        $splashImageRepository = $this->splashImageRepositoryMock();
        $splashImageValidator = $this->createSplashImageValidatorMock(array('image'=>'Please upload a valid image.'));
        $result = (new UpdateSplashImageUsecase(
            $splashImageRepository,
            $splashImageValidator,
            new Purifier(),
            new Errors()
        )
        )->execute(new SplashImage(), $params);
        $errors = new Errors();
        $errors->setError('Please upload a valid image.', 'image');
        $this->assertEquals($errors->getErrorMessages(), $result->getErrorMessages());
    }

    public function testUpdateSplashImageUsecaseReturnsErrorsObjectWithInvalidInputPassed()
    {
        $splashImageRepository = $this->splashImageRepositoryMock();
        $splashImageValidator = new SplashImageValidator(new Validator());
        $result = (new UpdateSplashImageUsecase(
            $splashImageRepository,
            $splashImageValidator,
            new Purifier(),
            new Errors()
        ))->execute(new SplashImage(), array());
        $errors = new Errors();
        $errors->setError('Invalid Parameters');
        $this->assertEquals($errors->getErrorMessages(), $result->getErrorMessages());
    }

    public function testUpdateSplashImageUsecaseReturnsSplashImageObjectWhenValidInputPassed()
    {
        $params = array(
            'image' => 'test.jpg',
            'position' => 1
        );
        $splashImageRepository = $this->splashImageRepositoryMockWithSaveMethod();
        $splashImageValidator = $this->createSplashImageValidatorMock(true);
        $result = (new UpdateSplashImageUsecase(
            $splashImageRepository,
            $splashImageValidator,
            new Purifier(),
            new Errors()
        )
        )->execute(new SplashImage(), $params);
        $this->assertEquals(new SplashImage(), $result);
    }

    private function splashImageRepositoryMock()
    {
        $splashImageRepositoryMock = $this->getMock('\Core\Domain\Repository\SplashImageRepositoryInterface');
        return $splashImageRepositoryMock;
    }

    private function splashImageRepositoryMockWithSaveMethod()
    {
        $splashImageRepositoryMock = $this->splashImageRepositoryMock();
        $splashImageRepositoryMock
            ->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf('\Core\Domain\Entity\User\SplashImage'))
            ->willReturn(new SplashImage());
        return $splashImageRepositoryMock;
    }

    private function createSplashImageValidatorMock($returns)
    {
        $mockSplashImageValidator = $this->getMockBuilder('\Core\Domain\Validator\SplashImageValidator')
            ->disableOriginalConstructor()
            ->getMock();
        $mockSplashImageValidator->expects($this->once())
            ->method('validate')
            ->with($this->isInstanceOf('\Core\Domain\Entity\User\SplashImage'))
            ->willReturn($returns);
        return $mockSplashImageValidator;
    }
}
