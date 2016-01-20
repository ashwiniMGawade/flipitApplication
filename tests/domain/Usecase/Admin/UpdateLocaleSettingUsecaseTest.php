<?php
namespace Usecase\Admin;

use \Core\Domain\Entity\LocaleSettings;
use \Core\Domain\Service\Validator;
use \Core\Domain\Usecase\Admin\UpdateLocaleSettingsUsecase;
use \Core\Domain\Validator\LocaleSettingValidator;
use \Core\Domain\Service\Purifier;
use \Core\Service\Errors;

class UpdateLocaleSettingsUsecaseTest extends \Codeception\TestCase\Test
{
    public function testUpdateLocaleSettingsUsecaseReturnsErrorWhenParamsAreEmpty()
    {
        $params = array();
        $localeSettingRepository = $this->localeSettingRepositoryMock();
        $localeSettingValidator = new LocaleSettingValidator(new Validator());
        $result = (new UpdateLocaleSettingsUsecase(
            $localeSettingRepository,
            $localeSettingValidator,
            new Purifier(),
            new Errors()
        ))->execute(new LocaleSettings(), $params);
        $errors = new Errors();
        $errors->setError('Invalid Parameters');
        $this->assertEquals($errors->getErrorMessages(), $result->getErrorMessages());
    }

    public function testUpdateLocaleSettingsUsecaseReturnsErrorWhenParamsAreInvalid()
    {
        $params = array(
            'locale' => null
        );
        $localeSettingRepository = $this->localeSettingRepositoryMock();
        $localeSettingValidator = $this->createLocaleSettingValidatorMock(array('locale' => 'locale cannot be empty.'));
        $result = (new UpdateLocaleSettingsUsecase(
            $localeSettingRepository,
            $localeSettingValidator,
            new Purifier(),
            new Errors()
        ))->execute(new LocaleSettings(), $params);
        $errors = new Errors();
        $errors->setError('locale cannot be empty.', 'locale');
        $this->assertEquals($errors->getErrorMessages(), $result->getErrorMessages());
    }

    public function testUpdateLocaleSettingsUsecaseWhenParamsAreValid()
    {
        $params = array(
            'locale' => 'be',
            'expiredCouponLogo' => 'test',
            'timezone' => 'test'
        );
        $localeSettingRepository = $this->localeSettingRepositoryMockWithSaveMethod(new LocaleSettings());
        $localeSettingValidator = $this->createLocaleSettingValidatorMock(true);
        $result = (new UpdateLocaleSettingsUsecase(
            $localeSettingRepository,
            $localeSettingValidator,
            new Purifier(),
            new Errors()
        ))->execute(new LocaleSettings(), $params);
        $this->assertInstanceOf('\Core\Domain\Entity\LocaleSettings', $result);
    }

    private function localeSettingRepositoryMock()
    {
        $localeSettingRepositoryMock = $this->getMock('\Core\Domain\Repository\LocaleSettingRepositoryInterface');
        return $localeSettingRepositoryMock;
    }

    private function localeSettingRepositoryMockWithSaveMethod($returns)
    {
        $localeSettingRepositoryMock = $this->localeSettingRepositoryMock();
        $localeSettingRepositoryMock
            ->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf('\Core\Domain\Entity\LocaleSettings'))
            ->willReturn($returns);
        return $localeSettingRepositoryMock;
    }

    private function createLocaleSettingValidatorMock($returns)
    {
        $LocaleSettingValidator = $this->getMockBuilder('\Core\Domain\Validator\LocaleSettingValidator')
            ->disableOriginalConstructor()
            ->getMock();
        $LocaleSettingValidator->expects($this->once())
            ->method('validate')
            ->with($this->isInstanceOf('\Core\Domain\Entity\LocaleSettings'))
            ->willReturn($returns);
        return $LocaleSettingValidator;
    }
}
