<?php
namespace Validator;

use Core\Domain\Entity\LandingPages;
use Core\Domain\Validator\LandingPageValidator;

class LandingPageValidatorTest extends \Codeception\TestCase\Test
{
    public function testLandingPageValidatorWithValidOutcome()
    {
        $landingPageValidator = new LandingPageValidator($this->mockValidatorInterface(true));
        $this->assertTrue($landingPageValidator->validate(new LandingPages()));
    }

    public function testLandingPageValidatorWithInvalidOutcome()
    {
        $landingPageValidator = new LandingPageValidator($this->mockValidatorInterface(false));
        $this->assertFalse($landingPageValidator->validate(new LandingPages()));
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
