<?php
namespace Service;


use Core\Domain\Service\KeyGenerator;

class KeyGeneratorTest extends \Codeception\TestCase\Test
{
    /**
     * @var \DomainTester
     */
    protected $tester;

    public function testThrowsAnExceptionWhenLengthIsLessThan8AndGreaterThan65()
    {
        $this->setExpectedException('Exception', 'The API Key length must be between 8 and 64 characters');
        ((new KeyGenerator())->generate(6));
        ((new KeyGenerator())->generate(null));
        ((new KeyGenerator())->generate(66));
    }

    public function testDefaultApiKeyLengthIsEqualTo32WhenLengthIsNotPassed()
    {
        $apiKey = (new KeyGenerator())->generate();
        $this->assertEquals(32, strlen($apiKey));
    }

    public function testApiKeyLengthIsEqualToTheParameterValueWhenValidParameterIsPassed()
    {
        $apiKey = (new KeyGenerator())->generate(34);
        $this->assertEquals(34, strlen($apiKey));
    }
}
