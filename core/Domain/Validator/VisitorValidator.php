<?php
namespace Core\Domain\Validator;

use Core\Domain\Adapter\ValidatorInterface;
use Core\Domain\Entity\Visitor;

class VisitorValidator
{
    protected $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validate(Visitor $visitor)
    {
        $rules = array(
            'inactiveStatusReason' => array(
                $this->validator->type(array('type' => 'string'))
            ),
            'active' => array(
                $this->validator->type(array('type' => 'integer')),
                $this->validator->length(array('min' => 1, 'max' => 1))
            ),
            'lastEmailOpenDate' => array(
                $this->validator->type(array('type' => 'integer')),
                $this->validator->length(array('min' => 11, 'max' => 11))
            )
        );
        return $this->validator->validate($visitor, $rules);
    }
}
