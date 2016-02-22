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
                $this->validator->notNull(array('message' => 'Content should not be blank.'))
            ),
            'image' => array(
                $this->validator->notNull(array('message' => 'Please upload a valid banner image.'))
            ),
            'popularShops' => array(
                $this->validator->notNull(array('message' => 'Popular shops should not be blank.'))
            ),
            'infoImage' => array(
                $this->validator->notNull(array('message' => 'Please upload a valid splash info image.'))
            ),
            'footer' => array(
                $this->validator->notNull(array('message' => 'Footer content should not be blank.'))
            ),
            'statistics' => array(
                $this->validator->notNull(array('message' => 'Statistics should not be blank.'))
            )
        );
        return $constraints;
    }
}
