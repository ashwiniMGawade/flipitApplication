<?php
namespace Core\Domain\Validator;

use \Core\Domain\Adapter\ValidatorInterface;
use \Core\Domain\Entity\User\Splash;

class SplashOfferValidator
{
    protected $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validate(Splash $splashOffer)
    {
        $constraints = $this->setDefaultValidationRules();
        return $this->validator->validate($splashOffer, $constraints);
    }

    private function setDefaultValidationRules()
    {
        $constraints = array(
            'locale' => array(
                $this->validator->notNull(array('message'=>'Locale should not be blank.'))
            ),
            'shopId' => array(
                $this->validator->notNull(array('message'=>'Shop should not be blank.')),
                $this->validator->greaterThan(array('value' => 0, 'message' => 'Shop should not be blank.'))
            ),
            'offerId' => array(
                $this->validator->notNull(array('message'=>'Offer should not be blank.')),
                $this->validator->greaterThan(array('value' => 0, 'message' => 'Offer should not be blank.'))
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
