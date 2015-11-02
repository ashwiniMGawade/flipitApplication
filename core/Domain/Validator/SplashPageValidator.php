<?php
namespace Core\Domain\Validator;

use \Core\Domain\Adapter\ValidatorInterface;
use \Core\Domain\Entity\User\SplashPage;

class SplashPageValidator
{
    protected $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validate(SplashPage $splashPage)
    {
        $constraints = $this->setDefaultValidationRules();
        return $this->validator->validate($splashPage, $constraints);
    }

    private function setDefaultValidationRules()
    {
        $constraints = array(
            'content' => array(
                $this->validator->notNull(array('message'=>'Content should not be blank.'))
            ),
            'image' => array(
                $this->validator->notNull(array('message'=>'Image should not be blank.'))
            ),
            'popularShops' => array(
                $this->validator->notNull(array('message'=>'Popular shops should not be blank.'))
            ),
            'updatedBy' => array(
                $this->validator->notNull(array('message'=>'Updated by should not be blank.'))
            ),
            'updatedAt' => array(
                $this->validator->notNull(),
                $this->validator->dateTime()
            )
        );
        return $constraints;
    }
}
