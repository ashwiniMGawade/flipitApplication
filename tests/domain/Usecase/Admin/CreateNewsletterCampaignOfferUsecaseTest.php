<?php
namespace Usecase\Admin;

use Core\Domain\Usecase\Admin\CreateNewsletterCampaignOfferUsecase;

class CreateNewsletterCampaignOfferUsecaseTest extends \Codeception\TestCase\Test
{
    public function testCreateNewsletterCampaignOfferUsecase()
    {
        $this->assertInstanceOf(
            '\Core\Domain\Entity\NewsletterCampaignOffer',
            (new CreateNewsletterCampaignOfferUsecase())->execute()
        );
    }
}
