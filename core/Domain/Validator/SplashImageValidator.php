<?php
namespace Core\Domain\Validator;

use \Core\Domain\Adapter\ValidatorInterface;
use \Core\Domain\Entity\User\SplashImage;

class SplashImageValidator
{
    protected $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validate(SplashImage $splashImage)
    {
        $constraints = $this->setDefaultValidationRules();
        return $this->validator->validate($splashImage, $constraints);
    }

    private function setDefaultValidationRules()
    {
        $constraints = array(
            'image' => array(
                $this->validator->notNull(array('message'=>'Please upload a valid image.'))
            ),
            'position' => array(
                $this->validator->notNull(array('message'=>'Position should not be blank.')),
                $this->validator->greaterThan(array('value' => 0, 'message' => 'Position should not be blank.'))
            ),
            'created_at' => array(
                $this->validator->notNull(),
                $this->validator->dateTime()
            )
        );
        return $constraints;
    }
}
