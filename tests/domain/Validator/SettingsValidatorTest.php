<?php
namespace Validator;

use \Core\Domain\Entity\Settings;
use \Core\Domain\Service\Validator;
use \Core\Domain\Validator\SettingsValidator;

class SettingsValidatorTest extends \Codeception\TestCase\Test
{
    public function testHtmlLangSettingWithValidInput()
    {
        $setting = new Settings();
        $setting->setName('HTML_LANG');
        $setting->setValue('en');
        $settingsValidator = new SettingsValidator($this->mockValidatorInterface(true));
        $this->assertTrue($settingsValidator->validate($setting));
    }

    public function testSettingValidatorForInvalidSetting()
    {
        $setting = new Settings();
        $settingsValidator = new SettingsValidator($this->mockValidatorInterface(true));
        $this->assertTrue($settingsValidator->validate($setting));
    }

    private function mockValidatorInterface($flag)
    {
        $mockValidatorInterface = $this->getMock('\Core\Domain\Adapter\ValidatorInterface');
        $mockValidatorInterface
            ->expects($this->once())
            ->method('validate')
            ->with($this->isType('object'), $this->isType('array'))
            ->willReturn($flag);
        return $mockValidatorInterface;
    }
}
