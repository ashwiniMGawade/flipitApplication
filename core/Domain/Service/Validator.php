<?php
namespace Core\Domain\Service;

use \Core\Domain\Adapter\ValidatorInterface;
use \Symfony\Component\Validator\Validation;
use \Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Validator
 *
 * @package Core\Domain\Service
 */
class Validator implements ValidatorInterface
{
    /**
     * @var \Symfony\Component\Validator\ValidatorInterface
     */
    protected $validator;

    /**
     *  Gets the Validator instance
     */
    public function __construct()
    {
        $this->validator = Validation::createValidator();
    }

    /**
     * @param $entity
     * @param $constraint
     *
     * @return array|bool
     * @throws \Exception
     */
    public function validate($entity, $constraint)
    {
        if (!is_object($entity) || !is_array($constraint)) {
            throw new \Exception('Unexpected Parameter types');
        }
        $error = array();
        foreach ($constraint as $propertyName => $propertyConstraints) {
            $error[$propertyName] = $this->validator->validate($entity->$propertyName, $propertyConstraints);
        }
        foreach ($error as $property => $violationList) {
            if (!$violationList->count()) {
                unset($error[$property]);
                continue;
            }
        }
        return (count($error)) ? $error : true;
    }

    /**
     * @param array $options
     *
     * @return \Symfony\Component\Validator\Constraints\Collection
     */
    public function collection($options = array())
    {
        return new Assert\Collection($options);
    }

    /**
     * @return \Symfony\Component\Validator\Constraints\NotBlank
     */
    public function notNull()
    {
        return new Assert\NotBlank();
    }

    /**
     * @param array $options
     *
     * @return \Symfony\Component\Validator\Constraints\Length
     */
    public function length($options = array())
    {
        return new Assert\Length($options);
    }

    /**
     * @param array $options
     *
     * @return \Symfony\Component\Validator\Constraints\Type
     */
    public function type($options = array())
    {
        return new Assert\Type($options);
    }

    /**
     * @return \Symfony\Component\Validator\Constraints\DateTime
     */
    public function dateTime()
    {
        return new Assert\DateTime();
    }
}
