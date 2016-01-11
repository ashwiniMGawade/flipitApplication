<?php
namespace Core\Domain\Validator;

use \Core\Domain\Adapter\ValidatorInterface;
use \Core\Domain\Entity\NewsletterCampaignOffer;

class NewsletterCampaignOfferValidator
{
    protected $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validate(NewsletterCampaignOffer $newsletterCampaignOffer)
    {
        $constraints = $this->setDefaultValidationRules();
        return $this->validator->validate($newsletterCampaignOffer, $constraints);
    }

    private function setDefaultValidationRules()
    {
        $constraints = array(
            'newsletterCampaign' => array(
                $this->validator->notNull(array('message'=>'Newsletter campaign should not be blank.')),
                $this->validator->type(array('type' => 'object'))
            ),
            'position' => array(
                $this->validator->notNull(array('message'=>'Position should not be blank.')),
                $this->validator->greaterThan(array('value' => 0, 'message' => 'Position should be numeric and greater than zero.'))
            ),
            'section' => array(
                $this->validator->type(array('type' => 'integer')),
                $this->validator->length(array('min' => 1, 'max' => 1))
            ),
            'deleted' => array(
                $this->validator->type(array('type' => 'integer')),
                $this->validator->length(array('min' => 1, 'max' => 1))
            ),
            'createdAt' => array(
                $this->validator->notNull(array('message' => "Please enter Created date")),
                $this->validator->dateTime()
            ),
            'updatedAt' => array(
                $this->validator->dateTime()
            )
        );

        return $constraints;
    }
}
