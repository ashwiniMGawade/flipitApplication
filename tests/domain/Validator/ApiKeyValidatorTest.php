<?php
namespace Validator;

use \Core\Domain\Entity\User\ApiKey;
use \Core\Domain\Validator\ApiKeyValidator;

/**
 * Class ApiKeyValidatorTest
 *
 * @package Validator
 */
class ApiKeyValidatorTest extends \Codeception\TestCase\Test
{
    /**
     * @var \DomainTester
     */
    protected $tester;

    /**
     *  Test Api Key Validator With Valid Outcome
     */
    public function testApiKeyValidatorWithValidOutcome()
    {
        $apiKeyValidator = new ApiKeyValidator($this->mockValidatorInterface(true));
        $this->assertTrue($apiKeyValidator->validate(new ApiKey()));
    }

    /**
     *  Test Api Key Validator With Invalid Outcome
     */
    public function testApiKeyValidatorWithInvalidOutcome()
    {
        $apiKeyValidator = new ApiKeyValidator($this->mockValidatorInterface(false));
        $this->assertFalse($apiKeyValidator->validate(new ApiKey()));
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
