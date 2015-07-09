<?php
namespace Core\Domain\Validator;

use \Core\Domain\Adapter\ValidatorInterface;
use \Core\Domain\Entity\User\ApiKey;

/**
 * Class ApiKeyValidator
 *
 * @package Core\Domain\Validator
 */
class ApiKeyValidator
{
    /**
     * @var \Core\Domain\Adapter\ValidatorInterface
     */
    protected $validator;

    /**
     * @param \Core\Domain\Adapter\ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param \Core\Domain\Entity\User\ApiKey $apiKey
     *
     * @return mixed
     */
    public function validate(ApiKey $apiKey)
    {
        $constraint = $this->setDefaultValidationRules();
        return $this->validator->validate($apiKey, $constraint);
    }

    /**
     * @return array
     */
    private function setDefaultValidationRules()
    {
        $rules = array(
            'api_key' => array(
                $this->validator->notNull(),
                $this->validator->length(array('min' => 32, 'max' => 32))
            ),
            'user_id' => array(
                $this->validator->notNull(),
                $this->validator->type(array('type' => 'object'))
            ),
            'created_at' => array(
                $this->validator->notNull(),
                $this->validator->dateTime()
            ),
            'deleted' => array(
                $this->validator->type(array('type' => 'integer')),
                $this->validator->length(array('min' => 1, 'max' => 1))
            )
        );
        return $rules;
    }
}
