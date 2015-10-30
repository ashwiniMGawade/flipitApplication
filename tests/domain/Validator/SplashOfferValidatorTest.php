<?php
namespace Validator;

use \Core\Domain\Entity\User\Splash;
use \Core\Domain\Validator\SplashOfferValidator;

/**
 * Class ShopOfferValidatorTest
 *
 * @package Validator
 */
class SplashOfferValidatorTest extends \Codeception\TestCase\Test
{
    /**
     * @var \DomainTester
     */
    protected $tester;

    /**
     *  Test Api Key Validator With Valid Outcome
     */
    public function testSplashOfferValidatorWithValidOutcome()
    {
        $splashOfferValidator = new SplashOfferValidator($this->mockValidatorInterface(true));
        $this->assertTrue($splashOfferValidator->validate(new Splash()));
    }

    /**
     *  Test Api Key Validator With Invalid Outcome
     */
    public function testSplashOfferValidatorWithInvalidOutcome()
    {
        $splashOfferValidator = new SplashOfferValidator($this->mockValidatorInterface(false));
        $this->assertFalse($splashOfferValidator->validate(new Splash()));
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
