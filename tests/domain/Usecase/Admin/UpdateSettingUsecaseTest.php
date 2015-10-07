<?php
namespace Usecase\Admin;

use \Core\Domain\Entity\Settings;
use \Core\Domain\Service\Purifier;
use \Core\Domain\Usecase\Admin\UpdateSettingUsecase;
use \Core\Domain\Service\Validator;
use \Core\Domain\Validator\SettingsValidator;
use \Core\Service\Errors;

class UpdateSettingUsecaseTest extends \Codeception\TestCase\Test
{
    public function testUpdateSettingUsecaseReturnsSettingsObject()
    {
        $params = array(
            'value' => null
        );
        $settingRepository = $this->settingRepositoryMockWithSaveMethod();
        $settingsValidator = new SettingsValidator(new Validator());
        $result = (new UpdateSettingUsecase(
            $settingRepository,
            $settingsValidator,
            new Purifier(),
            new Errors()
        )
        )->execute(new Settings(), $params);
        $this->assertEquals(new Settings(), $result);
    }

    public function testUpdateSettingUsecaseReturnsErrorWhenHtmlLangSettingHasNonAlphaValue()
    {
        $params = array(
            'value' => 'Test123'
        );
        $setting = new Settings();
        $setting->setName('HTML_LANG');
        $settingRepository = $this->settingRepositoryMock();
        $settingsValidator = new SettingsValidator(new Validator());
        $result = (new UpdateSettingUsecase(
            $settingRepository,
            $settingsValidator,
            new Purifier(),
            new Errors()
        )
        )->execute($setting, $params);
        $errors = new Errors();
        $errors->setError('HTML lang must contain only alphabets', 'value');
        $this->assertEquals($errors->getErrorMessages(), $result->getErrorMessages());
    }

    private function settingRepositoryMock()
    {
        $settingRepositoryMock = $this->getMock('\Core\Domain\Repository\SettingsRepositoryInterface');
        return $settingRepositoryMock;
    }

    private function settingRepositoryMockWithSaveMethod()
    {
        $settingRepositoryMock = $this->settingRepositoryMock();
        $settingRepositoryMock
            ->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf('\Core\Domain\Entity\Settings'))
            ->willReturn(new Settings());
        return $settingRepositoryMock;
    }
}
