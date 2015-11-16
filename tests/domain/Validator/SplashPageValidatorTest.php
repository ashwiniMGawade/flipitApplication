<?php
namespace Validator;

use \Core\Domain\Entity\User\SplashPage;
use \Core\Domain\Validator\SplashPageValidator;

class SplashPageValidatorTest extends \Codeception\TestCase\Test
{
    protected $tester;

    public function testSplashPageValidatorWithValidOutcome()
    {
        $splashPageValidator = new SplashPageValidator($this->mockValidatorInterface(true));
        $this->assertTrue($splashPageValidator->validate(new SplashPage()));
    }

    public function testSplashPageValidatorWithInvalidOutcome()
    {
        $splashPageValidator = new SplashPageValidator($this->mockValidatorInterface(false));
        $this->assertFalse($splashPageValidator->validate(new SplashPage()));
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
