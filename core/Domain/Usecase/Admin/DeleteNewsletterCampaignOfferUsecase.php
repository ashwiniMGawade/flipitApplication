<?php
namespace Core\Domain\Usecase\Admin;

use \Core\Domain\Entity\NewsletterCampaignOffer;
use \Core\Domain\Repository\NewsletterCampaignOfferRepositoryInterface;

class DeleteNewsletterCampaignOfferUsecase
{

    private $newsletterCampaignOfferRepository;

    public function __construct(NewsletterCampaignOfferRepositoryInterface $newsletterCampaignOfferRepository)
    {
        $this->newsletterCampaignOfferRepository = $newsletterCampaignOfferRepository;
    }

    public function execute(NewsletterCampaignOffer $newsletterCampaignOffer)
    {
        return $this->newsletterCampaignOfferRepository->remove($newsletterCampaignOffer);
    }
}
