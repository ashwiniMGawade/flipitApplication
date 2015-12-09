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
            'visitorsPerMonthCount' => array(
                $this->validator->notNull(array('message' => 'Visitor per month count should not be blank.')),
                $this->validator->type(array('type' => 'integer', 'message' => 'Visitor per month count must be an integer.'))
            ),
            'verifiedActionCount' => array(
                $this->validator->notNull(array('message' => 'Verified action count should not be blank.')),
                $this->validator->type(array('type' => 'integer', 'message' => 'Verified action count must be an integer.'))
            ),
            'newsletterSignupCount' => array(
                $this->validator->notNull(array('message' => 'Newsletter signup count should not be blank.')),
                $this->validator->type(array('type' => 'integer', 'message' => 'Newsletter signup count must be an integer.'))
            ),
            'retailerOnlineCount' => array(
                $this->validator->notNull(array('message' => 'Retailer online count should not be blank.')),
                $this->validator->type(array('type' => 'integer', 'message' => 'Retailer online count must be an integer.'))
            ),
        );
        return $constraints;
    }
}
