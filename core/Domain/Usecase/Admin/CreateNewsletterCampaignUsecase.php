<?php
namespace Core\Domain\Usecase\Admin;

use Core\Domain\Entity\NewsletterCampaign;

class CreateNewsletterCampaignUsecase
{
    public function execute()
    {
        return new NewsletterCampaign();
    }
}
