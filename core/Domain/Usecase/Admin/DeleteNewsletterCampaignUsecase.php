<?php
namespace Core\Domain\Usecase\Admin;

use \Core\Domain\Entity\NewsletterCampaign;
use \Core\Domain\Repository\NewsletterCampaignRepositoryInterface;

class DeleteNewsletterCampaignUsecase
{

    private $newsletterCampaignRepository;

    public function __construct(NewsletterCampaignRepositoryInterface $newsletterCampaignRepository)
    {
        $this->newsletterCampaignRepository = $newsletterCampaignRepository;
    }

    public function execute(NewsletterCampaign $newsletterCampaign)
    {
        return $this->newsletterCampaignRepository->remove($newsletterCampaign);
    }
}
