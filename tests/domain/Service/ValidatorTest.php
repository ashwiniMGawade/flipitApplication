<?php
namespace Service;

use Core\Domain\Entity\LandingPage;
use \Core\Domain\Entity\User\ApiKey;
use \Core\Domain\Entity\Visitor;
use \Core\Domain\Service\Validator;

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
        $apiKey = new ApiKey();
        $apiKey->__set('api_key', 'CjhPh4@^dhsUXcysL^3EzMKqTzNorw@g');

        $validator = new Validator();
        $rules = array(
            'api_key' => array(
                $validator->notNull()
            )
        );

        (new Validator())->validate($apiKey, $rules);
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
                $validator->notNull(),
                $validator->length(array('min' => 32, 'max' => 32))
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

    public function testValidatorReturnsViolationForPropertyNotEqualsDateTime()
    {
        $apiKey = new ApiKey();
        $apiKey->__set('created_at', 123);

        $validator = new Validator();
        $rules = array(
            'created_at' => array(
                $validator->dateTime()
            )
        );
        $expected = array(
            "created_at" => array(
                'This value is not a valid datetime.'
            )
        );
        $response = (new Validator())->validate($apiKey, $rules);
        $this->assertEquals($expected, $response);
    }

    public function testValidatorReturnsViolationForPropertyGreaterThanValuePassed()
    {
        $apiKey = new ApiKey();
        $apiKey->__set('id', 1);

        $validator = new Validator();
        $rules = array(
            'id' => array(
                $validator->greaterThan(array('value' => 2))
            )
        );
        $expected = array(
            "id" => array(
                'This value should be greater than 2.'
            )
        );
        $response = (new Validator())->validate($apiKey, $rules);
        $this->assertEquals($expected, $response);
    }

    /**
     * @throws \Exception
     */
    public function testValidatorReturnsViolationForPropertyEqualsEmail()
    {
        $visitor = new Visitor();
        $visitor->setEmail('invalid');

        $validator = new Validator();
        $rules = array(
            'email' => array(
                $validator->email(),
            )
        );
        $expected = array(
            "email" => array(
                'This value is not a valid email address.'
            )
        );
        $response = (new Validator())->validate($visitor, $rules);
        $this->assertEquals($expected, $response);
    }

    /**
     * @throws \Exception
     */
    public function testValidatorReturnsViolationForPropertyEqualsUrl()
    {
        $landingPage = new LandingPage();
        $landingPage->setRefUrl('invalid');

        $validator = new Validator();
        $rules = array(
            'refUrl' => array(
                $validator->url(),
            )
        );
        $expected = array(
            "refUrl" => array(
                'This value is not a valid URL.'
            )
        );
        $response = (new Validator())->validate($landingPage, $rules);
        $this->assertEquals($expected, $response);
    }
}
