<?php
namespace Core\Domain\Validator;

use \Core\Domain\Adapter\ValidatorInterface;
use \Core\Domain\Entity\NewsletterCampaign;

class NewsletterCampaignValidator
{
    protected $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validate(NewsletterCampaign $newsletterCampaign)
    {
        $constraints = $this->setDefaultValidationRules();
        return $this->validator->validate($newsletterCampaign, $constraints);
    }

    private function setDefaultValidationRules()
    {
        $constraints = array(
            'campaignName' => array(
                $this->validator->type(array('type' => 'string', 'message' => "Please enter valid string.")),
                $this->validator->length(array( 'max' => 255 ))
            ),
            'campaignSubject' => array(
                $this->validator->type(array('type' => 'string', 'message' => "Please enter valid string.")),
                $this->validator->length(array( 'max' => 255 ))
            ),
            'senderName' => array(
                $this->validator->type(array('type' => 'string', 'message' => "Please enter valid string.")),
                $this->validator->length(array( 'max' => 255 ))
            ),
            'headerBanner' => array(
                $this->validator->type(array('type' => 'string', 'message' => "Please enter valid string.")),
                $this->validator->length(array( 'max' => 255 ))
            ),
            'headerBannerURL' => array(
                $this->validator->type(array('type' => 'string', 'message' => "Please enter valid string.")),
                $this->validator->length(array( 'max' => 255 ))
            ),
            'footerBanner' => array(
                $this->validator->type(array('type' => 'string', 'message' => "Please enter valid string.")),
                $this->validator->length(array( 'max' => 255 ))
            ),
            'footerBannerURL' => array(
                $this->validator->type(array('type' => 'string', 'message' => "Please enter valid string.")),
                $this->validator->length(array( 'max' => 255 ))
            ),
            'offerPartOneTitle' => array(
                $this->validator->type(array('type' => 'string', 'message' => "Please enter valid string.")),
                $this->validator->length(array( 'max' => 255 ))
            ),
            'offerPartTwoTitle' => array(
                $this->validator->type(array('type' => 'string', 'message' => "Please enter valid string.")),
                $this->validator->length(array( 'max' => 255 ))
            ),
            'senderEmail' => array(
                $this->validator->email(array('message' => "Please enter valid email ID")),
                $this->validator->length(array( 'max' => 255 ))
            ),
            'scheduledStatus' => array(
                $this->validator->type(array('type' => 'integer')),
                $this->validator->length(array('min' => 1, 'max' => 1))
            ),
            'scheduledTime' => array(
                $this->validator->dateTime(array('message' => "Please enter valid scheduled time"))
            ),
            'newsletterSentTime' => array(
                $this->validator->dateTime()
            ),
            'receipientCount' => array(
                $this->validator->type(array('type' => 'integer'))
            ),
            'deleted' => array(
                $this->validator->type(array('type' => 'integer')),
                $this->validator->length(array('min' => 1, 'max' => 1))
            ),
            'createdAt' => array(
                $this->validator->notNull(array('message' => "Please enter value")),
                $this->validator->dateTime()
            )
        );


        return $constraints;
    }
}
