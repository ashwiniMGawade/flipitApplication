<?php
namespace Validator;

use Core\Domain\Entity\URLSetting;
use Core\Domain\Validator\UrlSettingValidator;

class UrlSettingValidatorTest extends \Codeception\TestCase\Test
{
    public function testUrlSettingValidatorWithValidOutcome()
    {
        $urlSettingValidator = new UrlSettingValidator($this->mockValidatorInterface(true));
        $this->assertTrue($urlSettingValidator->validate(new URLSetting()));
    }

    public function testUrlSettingValidatorWithInvalidOutcome()
    {
        $urlSettingValidator = new UrlSettingValidator($this->mockValidatorInterface(false));
        $this->assertFalse($urlSettingValidator->validate(new URLSetting()));
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
