<?php
namespace Service;


use Core\Domain\Service\ApiKeyGenerator;

class ApiKeyGeneratorTest extends \Codeception\TestCase\Test
{
    /**
     * @var \DomainTester
     */
    protected $tester;

    public function testThrowsAnExceptionWhenLengthIsLessThan8AndGreaterThan65()
    {
        $this->setExpectedException('Exception', 'The API Key length must be between 8 and 64 characters');
        ((new ApiKeyGenerator())->generate(6));
        ((new ApiKeyGenerator())->generate(null));
        ((new ApiKeyGenerator())->generate(66));
    }

    public function testDefaultApiKeyLengthIsEqualTo32WhenLengthIsNotPassed()
    {
        $apiKey = (new ApiKeyGenerator())->generate();
        $this->assertEquals(32, strlen($apiKey));
    }

    public function testApiKeyLengthIsEqualToTheParameterValueWhenValidParameterIsPassed()
    {
        $apiKey = (new ApiKeyGenerator())->generate(34);
        $this->assertEquals(34, strlen($apiKey));
    }
}
