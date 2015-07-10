<?php
namespace Service;

use Core\Domain\Entity\User\ApiKey;
use Core\Domain\Service\Validator;

/**
 * Class ValidatorTest
 *
 * @package Service
 */
class ValidatorTest extends \Codeception\TestCase\Test
{
    /**
     * @var \DomainTester
     */
    protected $tester;

    /**
     * @throws \Exception
     */
    public function testValidateMethodWithInvalidParameters()
    {
        $this->setExpectedException('Exception', 'Unexpected Parameter types');
        (new Validator())->validate('Entity', 'Constraint');
        (new Validator())->validate('Entity', array());
        (new Validator())->validate(new \stdClass(), 'Constraint');
    }

    /**
     * @throws \Exception
     */
    public function testValidateMethodWithValidParameters()
    {
        (new Validator())->validate(new \stdClass(), array());
    }

    /**
     * @throws \Exception
     */
    public function testValidatorReturnsViolationForPropertyEqualsNull()
    {
        $apiKey = new ApiKey();
        $apiKey->__set('api_key', null);

        $validator = new Validator();
        $rules = array(
            'api_key' => array(
                $validator->notNull()
            )
        );
        $expected = array(
            "api_key" => array(
                'This value should not be blank.'
            )
        );
        $response = (new Validator())->validate($apiKey, $rules);
        $this->assertEquals($expected, $response);
    }

    /**
     * @throws \Exception
     */
    public function testValidatorReturnsViolationForPropertyWithLengthLessThanExpectedCharacters()
    {
        $apiKey = new ApiKey();
        $apiKey->__set('api_key', 123);

        $validator = new Validator();
        $rules = array(
            'api_key' => array(
                $validator->length(array('min' => 32, 'max' => 32))
            )
        );
        $expected = array(
            "api_key" => array(
                'This value should have exactly 32 characters.'
            )
        );
        $response = (new Validator())->validate($apiKey, $rules);
        $this->assertEquals($expected, $response);
    }

    /**
     * @throws \Exception
     */
    public function testValidatorReturnsViolationForPropertyNotEqualsExpectedType()
    {
        $apiKey = new ApiKey();
        $apiKey->__set('user_id', 123);

        $validator = new Validator();
        $rules = array(
            'user_id' => array(
                $validator->type(array('type' => 'object'))
            )
        );
        $expected = array(
            "user_id" => array(
                'This value should be of type object.'
            )
        );
        $response = (new Validator())->validate($apiKey, $rules);
        $this->assertEquals($expected, $response);
    }
}
