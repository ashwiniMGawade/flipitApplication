<?php
namespace Core\Domain\Validator;

use \Core\Domain\Adapter\ValidatorInterface;
use \Core\Domain\Entity\Shop;

class ShopValidator
{
    protected $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validate(Shop $shop)
    {
        $constraint = $this->setDefaultValidationRules();
        return $this->validator->validate($shop, $constraint);
    }

    private function setDefaultValidationRules()
    {
        $rules = array(
            'name' => array(
                $this->validator->notBlank(),
                $this->validator->length(array( 'max' => 255 ))
            ),
            'permaLink' => array(
                $this->validator->notNull(),
                $this->validator->length(array( 'max' => 255 ))
            ),
            'overriteTitle' => array(
                $this->validator->length(array( 'max' => 255 ))
            ),
            'title' => array(
                $this->validator->length(array( 'max' => 255 ))
            ),
            'subTitle' => array(
                $this->validator->length(array( 'max' => 255 ))
            ),
            'accountManagerName' => array(
                $this->validator->length(array( 'max' => 255 ))
            ),
            'created_at' => array(
                $this->validator->notNull(),
                $this->validator->dateTime()
            ),
            'updated_at' => array(
                $this->validator->notNull(),
                $this->validator->dateTime()
            ),
            'deleted' => array(
                $this->validator->type(array('type' => 'integer')),
                $this->validator->length(array('min' => 1, 'max' => 1))
            ),
            'classification' => array(
                $this->validator->type(array('type' => 'integer')),
                $this->validator->length(array('min' => 1, 'max' => 1))
            )
        );
        return $rules;
    }
}
