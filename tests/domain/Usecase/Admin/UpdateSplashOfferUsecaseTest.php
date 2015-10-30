<?php
namespace Usecase\Admin;

use Core\Domain\Entity\User\Splash;
use Core\Domain\Service\Purifier;
use Core\Domain\Service\Validator;
use Core\Domain\Usecase\Admin\UpdateSplashOfferUsecase;
use Core\Domain\Validator\SplashOfferValidator;
use Core\Service\Errors;

class UpdateSplashOfferUsecaseTest extends \Codeception\TestCase\Test
{
    protected $tester;

    public function testUpdateSplashOfferUsecaseReturnsErrorWhenParamsAreEmpty()
    {
        $params = array();
        $splashOfferRepository = $this->splashOfferRepositoryMock();
        $splashOfferValidator = new SplashOfferValidator(new Validator());
        $result = (new UpdateSplashOfferUsecase(
            $splashOfferRepository,
            $splashOfferValidator,
            new Purifier(),
            new Errors()
        ))->execute(new Splash(), $params);
        $errors = new Errors();
        $errors->setError('Invalid Parameters');
        $this->assertEquals($errors->getErrorMessages(), $result->getErrorMessages());
    }

    public function testUpdateSplashOfferUsecaseReturnsErrorWhenParamsAreInvalid()
    {
        $params = array(
            'title' => null,
            'shop' => null,
            'permalink' => null
        );
        $splashOfferRepository = $this->splashOfferRepositoryMock();
        $splashOfferValidator = $this->createSplashOfferValidatorMock(array('locale' => 'Locale should not be blank.'));
        $result = (new UpdateSplashOfferUsecase(
            $splashOfferRepository,
            $splashOfferValidator,
            new Purifier(),
            new Errors()
        ))->execute(new Splash(), $params);
        $errors = new Errors();
        $errors->setError('Locale should not be blank.', 'locale');
        $this->assertEquals($errors->getErrorMessages(), $result->getErrorMessages());
    }

    public function testUpdateSplashOfferUsecaseWhenParamtersAreValid()
    {
        $params = array(
            'locale' => 'in',
            'shopId' => 42,
            'offerId' => 1212,
            'position' => 1
        );

        $splashOfferRepository = $this->splashOfferRepositoryMockWithSaveMethod(new Splash());
        $splashOfferValidator = $this->createSplashOfferValidatorMock(true);
        $result = (new UpdateSplashOfferUsecase(
            $splashOfferRepository,
            $splashOfferValidator,
            new Purifier(),
            new Errors()
        ))->execute(new Splash(), $params);
        $this->assertInstanceOf('\Core\Domain\Entity\User\Splash', $result);
    }

    private function splashOfferRepositoryMock()
    {
        $splashOfferRepositoryMock = $this->getMock('\Core\Domain\Repository\SplashOfferRepositoryInterface');
        return $splashOfferRepositoryMock;
    }

    private function splashOfferRepositoryMockWithSaveMethod($returns)
    {
        $splashOfferRepositoryMock = $this->splashOfferRepositoryMock();
        $splashOfferRepositoryMock
            ->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf('\Core\Domain\Entity\User\Splash'))
            ->willReturn($returns);
        return $splashOfferRepositoryMock;
    }

    private function createSplashOfferValidatorMock($returns)
    {
        $mockSplashOfferValidator = $this->getMockBuilder('\Core\Domain\Validator\SplashOfferValidator')
            ->disableOriginalConstructor()
            ->getMock();
        $mockSplashOfferValidator->expects($this->once())
            ->method('validate')
            ->with($this->isInstanceOf('\Core\Domain\Entity\User\Splash'))
            ->willReturn($returns);
        return $mockSplashOfferValidator;
    }
}
