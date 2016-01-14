<?php
namespace Usecase\System;

use \Core\Domain\Entity\LocaleSettings;
use \Core\Domain\Service\Purifier;
use \Core\Domain\Usecase\System\GetLocaleSettingsUsecase;
use \Core\Service\Errors;

class GetLocaleSetingsUsecaseTest extends \Codeception\TestCase\Test
{
    public function testGetLocaleSettingsUsecaseReturnsZeroWhenLocaleSettingDoesNotExist()
    {
        $expectedLocaleSettings = 0;
        $localeSettingRepository = $this->createLocaleSettingsRepositoryWithFindByMethodMock($expectedLocaleSettings);
        $localeSettings = (new GetLocaleSettingsUsecase($localeSettingRepository, new Purifier(), new Errors()))->execute();
        $this->assertEquals($expectedLocaleSettings, $localeSettings);
    }

    public function testGetLocaleSettingsUsecaseReturnsArrayWhenRecordExist()
    {
        $localeSetting = new LocaleSettings();
        $expectedResult = array($localeSetting);
        $localeSettingRepository = $this->createLocaleSettingsRepositoryWithFindByMethodMock($expectedResult);
        $localeSettings = (new GetLocaleSettingsUsecase($localeSettingRepository, new Purifier(), new Errors()))->execute();
        $this->assertEquals(count($expectedResult), count($localeSettings));
    }

    public function testGetLocaleSettingsUsecaseReturnsErrorWhenParametersAreInvalid()
    {
        $conditions = 'invalid';
        $localeSettingRepository = $this->createLocaleSettingRepositoryMock();
        $errors = new Errors();
        $errors->setError('Invalid input, unable to find record.');
        $result = (new GetLocaleSettingsUsecase($localeSettingRepository, new Purifier(), new Errors()))->execute($conditions);
        $this->assertInstanceOf('\Core\Service\Errors', $result);
        $this->assertEquals($errors->getErrorsAll(), $result->getErrorsAll());
    }

    private function createLocaleSettingRepositoryMock()
    {
        $localeSettingRepository = $this->getMock('\Core\Domain\Repository\LocaleSettingRepositoryInterface');
        return $localeSettingRepository;
    }

    private function createLocaleSettingsRepositoryWithFindByMethodMock($returns)
    {
        $localeSettingRepository = $this->createLocaleSettingRepositoryMock();
        $localeSettingRepository->expects($this->once())
            ->method('findBy')
            ->with($this->equalTo('\Core\Domain\Entity\LocaleSettings'), $this->isType('array'))
            ->willReturn($returns);
        return $localeSettingRepository;
    }
}
