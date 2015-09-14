<?php
namespace Core\Domain\Validator;

use Core\Domain\Adapter\ValidatorInterface;
use Core\Domain\Entity\LandingPages;

class LandingPageValidator
{
    protected $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validate(LandingPages $landingPage)
    {
        $constraints = $this->setDefaultValidationRules();
        return $this->validator->validate($landingPage, $constraints);
    }

    private function setDefaultValidationRules()
    {
        $constraints = array(
            'title' => array(
                $this->validator->notNull(array('message'=>'Title should not be blank.'))
            ),
            'shop' => array(
                $this->validator->notNull(array('message'=>'Shop should not be blank.')),
                $this->validator->type(array('type' => 'object'))
            ),
            'permalink' => array(
                $this->validator->notNull(array('message'=>'Permalink should not be blank.'))
            ),
            'subTitle' => array(
                $this->validator->notNull()
            ),
            'offlineSince' => array(
                $this->validator->dateTime()
            ),
            'status' => array(
                $this->validator->notNull(array('message'=>'Status should not be blank.')),
                $this->validator->type(array('type' => 'integer')),
                $this->validator->length(array('min' => 1, 'max' => 1))
            ),
            'createdAt' => array(
                $this->validator->dateTime()
            ),
            'updatedAt' => array(
                $this->validator->dateTime()
            )
        );
        return $constraints;
    }
}
