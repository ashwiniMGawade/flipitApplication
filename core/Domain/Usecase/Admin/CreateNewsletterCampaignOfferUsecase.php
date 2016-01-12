<?php
namespace Core\Domain\Usecase\Admin;

use Core\Domain\Entity\NewsletterCampaignOffer;

class CreateNewsletterCampaignOfferUsecase
{
    public function execute()
    {
        return new NewsletterCampaignOffer();
    }
}
