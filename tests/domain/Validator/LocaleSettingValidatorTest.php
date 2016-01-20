<?php
namespace Validator;

use Core\Domain\Entity\LocaleSettings;
use Core\Domain\Validator\LocaleSettingValidator;

class LocaleSettingValidatorTest extends \Codeception\TestCase\Test
{
    public function testLocaleSettingValidatorWithValidOutcome()
    {
        $localeSettingValidator = new LocaleSettingValidator($this->mockValidatorInterface(true));
        $this->assertTrue($localeSettingValidator->validate(new LocaleSettings()));
    }

    public function testLocaleSettingValidatorWithInvalidOutcome()
    {
        $localeSettingValidator = new LocaleSettingValidator($this->mockValidatorInterface(false));
        $this->assertFalse($localeSettingValidator->validate(new LocaleSettings()));
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
