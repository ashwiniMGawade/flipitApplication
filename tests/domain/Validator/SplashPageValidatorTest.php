<?php
namespace Validator;

use \Core\Domain\Entity\User\SplashPage;
use \Core\Domain\Validator\SplashPageValidator;

/**
 * Class SplashPageValidatorTest
 *
 * @package Validator
 */
class SplashPageValidatorTest extends \Codeception\TestCase\Test
{
    /**
     * @var \DomainTester
     */
    protected $tester;

    /**
     *  Test Api Key Validator With Valid Outcome
     */
    public function testSplashPageValidatorWithValidOutcome()
    {
        $splashPageValidator = new SplashPageValidator($this->mockValidatorInterface(true));
        $this->assertTrue($splashPageValidator->validate(new SplashPage()));
    }

    /**
     *  Test Api Key Validator With Invalid Outcome
     */
    public function testSplashPageValidatorWithInvalidOutcome()
    {
        $splashPageValidator = new SplashPageValidator($this->mockValidatorInterface(false));
        $this->assertFalse($splashPageValidator->validate(new SplashPage()));
    }

    /**
     * @param $flag
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
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
