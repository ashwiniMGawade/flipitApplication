<?php
namespace Validator;

use \Core\Domain\Entity\User\SplashImage;
use \Core\Domain\Validator\SplashImageValidator;

class SplashImageValidatorTest extends \Codeception\TestCase\Test
{
    protected $tester;

    public function testSplashImageValidatorWithValidOutcome()
    {
        $splashImageValidator = new SplashImageValidator($this->mockValidatorInterface(true));
        $this->assertTrue($splashImageValidator->validate(new SplashImage()));
    }

    public function testSplashImageValidatorWithInvalidOutcome()
    {
        $splashImageValidator = new SplashImageValidator($this->mockValidatorInterface(false));
        $this->assertFalse($splashImageValidator->validate(new SplashImage()));
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
