<?php
namespace Usecase\System;

use \Core\Domain\Entity\Settings;
use \Core\Domain\Service\Purifier;
use \Core\Domain\Usecase\System\GetSettingUsecase;
use \Core\Service\Errors;

class GetSettingUsecaseTest extends \Codeception\TestCase\Test
{
    public function testGetSettingUsecaseWhenIdDoesNotExist()
    {
        $condition = array('id' => 0);
        $settingRepositoryMock = $this->createSettingRepositoryWithFindMethodMock($condition, 0);
        $settingUsecase = new GetSettingUsecase($settingRepositoryMock, new Purifier(), new Errors());
        $result = $settingUsecase->execute($condition);
        $errors = new Errors();
        $errors->setError('Setting not found');
        $this->assertEquals($errors->getErrorsAll(), $result->getErrorsAll());
    }

    public function testGetWidgetUsecase()
    {
        $condition = array('id' => 0);
        $setting = new Settings();
        $setting->setId(0);
        $settingRepositoryMock = $this->createSettingRepositoryWithFindMethodMock($condition, $setting);
        $settingUsecase = new GetSettingUsecase($settingRepositoryMock, new Purifier(), new Errors());
        $result = $settingUsecase->execute($condition);
        $this->assertEquals($setting, $result);
    }

    public function testGetSettingUsecaseWhenIdIsInvalid()
    {
        $condition = 'invalid';
        $settingRepositoryMock = $this->createSettingsRepositoryMock();
        $settingUsecase = new GetSettingUsecase($settingRepositoryMock, new Purifier(), new Errors());
        $result = $settingUsecase->execute($condition);
        $errors = new Errors();
        $errors->setError('Invalid input, unable to find setting.');
        $this->assertEquals($errors->getErrorsAll(), $result->getErrorsAll());
    }

    private function createSettingsRepositoryMock()
    {
        $settingRepository = $this->getMockBuilder('\Core\Domain\Repository\SettingsRepositoryInterface')->getMock();
        return $settingRepository;
    }

    private function createSettingRepositoryWithFindMethodMock($condition, $returns)
    {
        $settingRepositoryMock = $this->createSettingsRepositoryMock();
        $settingRepositoryMock
            ->expects($this->once())
            ->method('findOneBy')
            ->with($this->equalTo('\Core\Domain\Entity\Settings'), $this->equalTo($condition))
            ->willReturn($returns);
        return $settingRepositoryMock;
    }
}
