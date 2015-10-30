<?php
namespace Usecase\Admin;

use Core\Domain\Entity\User\Splash;
use Core\Domain\Service\Purifier;
use Core\Domain\Service\Validator;
use Core\Domain\Usecase\Admin\AddSplashOfferUsecase;
use Core\Domain\Validator\SplashOfferValidator;
use Core\Service\Errors;

class AddSplashOfferUsecaseTest extends \Codeception\TestCase\Test
{
    protected $tester;

    public function testAddSplashOfferUsecaseReturnsErrorWhenParamsAreEmpty()
    {
        $params = array();
        $splashOfferRepository = $this->splashOfferRepositoryMock();
        $SplashOfferValidator = new SplashOfferValidator(new Validator());
        $result = (new AddSplashOfferUsecase(
            $splashOfferRepository,
            $SplashOfferValidator,
            new Purifier(),
            new Errors()
        ))->execute(new Splash(), $params);
        $errors = new Errors();
        $errors->setError('Invalid Parameters');
        $this->assertEquals($errors->getErrorMessages(), $result->getErrorMessages());
    }

    public function testAddSplashOfferUsecaseReturnsErrorWhenParamsAreInvalid()
    {
        $params = array(
            'locale' => null,
            'shopId' => null,
            'offerId' => null,
            'position' => null
        );
        $splashOfferRepository = $this->splashOfferRepositoryMock();
        $SplashOfferValidator = $this->createSplashOfferValidatorMock(array('locale' => 'Locale should not be blank.'));
        $result = (new AddSplashOfferUsecase(
            $splashOfferRepository,
            $SplashOfferValidator,
            new Purifier(),
            new Errors()
        ))->execute(new Splash(), $params);
        $errors = new Errors();
        $errors->setError('Locale should not be blank.', 'locale');
        $this->assertEquals($errors->getErrorMessages(), $result->getErrorMessages());
    }

    public function testAddSplashOfferUsecaseWithValidInput()
    {
        $params = array(
            'locale' => 'in',
            'shopId' => 42,
            'offerId' => 1212,
            'position' => 1
        );

        $splashOfferRepository = $this->splashOfferRepositoryMockWithSaveMethod(new Splash());
        $SplashOfferValidator = $this->createSplashOfferValidatorMock(true);
        $result = (new AddSplashOfferUsecase(
            $splashOfferRepository,
            $SplashOfferValidator,
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
