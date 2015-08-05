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
            'mailClickCount' => array(
                $this->validator->notNull(),
                $this->validator->type(array('type' => 'integer'))
            ),
            'mailOpenCount' => array(
                $this->validator->notNull(),
                $this->validator->type(array('type' => 'integer'))
            ),
            'mailHardBounceCount' => array(
                $this->validator->notNull(),
                $this->validator->type(array('type' => 'integer'))
            ),
            'mailSoftBounceCount' => array(
                $this->validator->notNull(),
                $this->validator->type(array('type' => 'integer'))
            ),
            'inactiveStatusReason' => array(
                $this->validator->notNull(),
                $this->validator->type(array('type' => 'string'))
            ),
            'active' => array(
                $this->validator->type(array('type' => 'integer')),
                $this->validator->length(array('min' => 1, 'max' => 1))
            ),
            'lastEmailOpenDate' => array(
                $this->validator->notNull()
            )
        );
        return $this->validator->validate($visitor, $rules);
    }
}
