<?php
namespace Usecase\Admin;

use \Core\Domain\Entity\Settings;
use \Core\Domain\Service\Purifier;
use \Core\Domain\Usecase\Admin\GetSettingsUsecase;
use \Core\Service\Errors;

class GetSettingsUsecaseTest extends \Codeception\TestCase\Test
{
    public function testGetSettingsUsecaseReturnsZeroWhenSettingDoesNotExist()
    {
        $expectedSettings = 0;
        $settingRepository = $this->createSettingsRepositoryWithFindByMethodMock($expectedSettings);
        $settings = (new GetSettingsUsecase($settingRepository, new Purifier(), new Errors()))->execute();
        $this->assertEquals($expectedSettings, $settings);
    }

    public function testGetSettingsUsecaseReturnsArrayWhenSettingExist()
    {
        $setting = new Settings();
        $expectedResult = array($setting);
        $settingRepository = $this->createSettingsRepositoryWithFindByMethodMock($expectedResult);
        $settings = (new GetSettingsUsecase($settingRepository, new Purifier(), new Errors()))->execute();
        $this->assertEquals(count($expectedResult), count($settings));
    }

    public function testGetSettingsUsecaseReturnsErrorWhenParametersAreInvalid()
    {
        $conditions = 'invalid';
        $settingRepository = $this->createSettingRepositoryMock();
        $errors = new Errors();
        $errors->setError('Invalid input, unable to find setting.');
        $result = (new GetSettingsUsecase($settingRepository, new Purifier(), new Errors()))->execute($conditions);
        $this->assertInstanceOf('\Core\Service\Errors', $result);
        $this->assertEquals($errors->getErrorsAll(), $result->getErrorsAll());
    }

    private function createSettingRepositoryMock()
    {
        $settingRepository = $this->getMock('\Core\Domain\Repository\SettingsRepositoryInterface');
        return $settingRepository;
    }

    private function createSettingsRepositoryWithFindByMethodMock($returns)
    {
        $settingRepository = $this->createSettingRepositoryMock();
        $settingRepository->expects($this->once())
            ->method('findBy')
            ->with($this->equalTo('\Core\Domain\Entity\Settings'), $this->isType('array'))
            ->willReturn($returns);
        return $settingRepository;
    }
}
