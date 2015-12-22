<?php
namespace Core\Domain\Usecase\Admin;

use \Core\Domain\Repository\NewsletterCampaignOfferRepositoryInterface;
use \Core\Domain\Adapter\PurifierInterface;
use \Core\Service\Errors\ErrorsInterface;

class GetNewsletterCampaignOfferUsecase
{
    protected $newsletterCampaignOfferRepository;

    protected $htmlPurifier;

    protected $errors;

    public function __construct(
        NewsletterCampaignOfferRepositoryInterface $newsletterCampaignOfferRepository,
        PurifierInterface $htmlPurifier,
        ErrorsInterface $errors
    ) {
        $this->newsletterCampaignRepository = $newsletterCampaignOfferRepository;
        $this->htmlPurifier     = $htmlPurifier;
        $this->errors           = $errors;
    }

    public function execute($conditions)
    {
        $conditions = $this->htmlPurifier->purify($conditions);
        if (!is_array($conditions)) {
            $this->errors->setError('Invalid input, unable to find campaign.');
            return $this->errors;
        }

        $newsletterCampaignOffer = $this->newsletterCampaignRepository->findOneBy('\Core\Domain\Entity\NewsletterCampaignOffer', $conditions);

        if (false === is_object($newsletterCampaignOffer)) {
            $this->errors->setError('Newsletter Campaign offer not found');
            return $this->errors;
        }
        return $newsletterCampaignOffer;
    }
}
