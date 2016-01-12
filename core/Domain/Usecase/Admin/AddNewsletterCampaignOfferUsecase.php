<?php
namespace Core\Domain\Usecase\Admin;

use \Core\Domain\Repository\NewsletterCampaignOfferRepositoryInterface;
use \Core\Domain\Entity\NewsletterCampaignOffer;
use \Core\Domain\Adapter\PurifierInterface;
use \Core\Domain\Validator\NewsletterCampaignOfferValidator;
use \Core\Service\Errors\ErrorsInterface;

class AddNewsletterCampaignOfferUsecase
{
    protected $newsletterCampaignOfferRepository;

    protected $newsletterCampaignOfferValidator;

    protected $htmlPurifier;

    protected $errors;

    public function __construct(
        NewsletterCampaignOfferRepositoryInterface $newsletterCampaignOfferRepository,
        NewsletterCampaignOfferValidator $newsletterCampaignOfferValidator,
        PurifierInterface $htmlPurifier,
        ErrorsInterface $errors
    ) {
        $this->newsletterCampaignOfferRepository = $newsletterCampaignOfferRepository;
        $this->newsletterCampaignOfferValidator  = $newsletterCampaignOfferValidator;
        $this->htmlPurifier     = $htmlPurifier;
        $this->errors           = $errors;
    }

    public function execute(NewsletterCampaignOffer $newsletterCampaignOffer, $params = array())
    {
        if (empty($params)) {
            $this->errors->setError('Invalid Parameters');
            return $this->errors;
        }

        $params = $this->htmlPurifier->purify($params);

        if (isset($params['newsletterCampaign'])) {
            $newsletterCampaignOffer->setNewsletterCampaign($params['newsletterCampaign']);
        }
        if (isset($params['offer'])) {
            $newsletterCampaignOffer->setOffer($params['offer']);
        }
        if (isset($params['offerId'])) {
            $newsletterCampaignOffer->setOfferId($params['offerId']);
        }
        if (isset($params['position'])) {
            $newsletterCampaignOffer->setPosition($params['position']);
        }
        if (isset($params['section'])) {
            $newsletterCampaignOffer->setSection((int)$params['section']);
        }
        if (isset($params['deleted'])) {
            $newsletterCampaignOffer->setDeleted($params['deleted']);
        }
        $newsletterCampaignOffer->setCreatedAt(new \DateTime('now'));
        $newsletterCampaignOffer->setUpdatedAt(new \DateTime('now'));

        $validationResult = $this->newsletterCampaignOfferValidator->validate($newsletterCampaignOffer);

        if (true !== $validationResult && is_array($validationResult)) {
            $this->errors->setErrors($validationResult);
            return $this->errors;
        }
        return $this->newsletterCampaignOfferRepository->addNewsletterCampaignOffer($newsletterCampaignOffer);
    }
}
