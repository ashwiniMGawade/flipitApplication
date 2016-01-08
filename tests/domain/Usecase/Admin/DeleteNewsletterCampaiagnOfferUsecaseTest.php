<?php
namespace Usecase\Admin;

use \Core\Domain\Entity\NewsletterCampaignOffer;
use \Core\Domain\Usecase\Admin\DeleteNewsletterCampaignOfferUsecase;

class DeleteNewsletterCampaignOfferUsecaseTest extends \Codeception\TestCase\Test
{
    public function testDeleteNewsletterCampaignOfferUsecase()
    {
        $newsletterCampaignOfferRepository = $this->createDeleteNewsletterCampaignOfferRepositoryInterfaceWithMethodsMock(true);
        $this->assertEquals(true, (new DeleteNewsletterCampaignOfferUsecase($newsletterCampaignOfferRepository))->execute([1, 2]));
    }

    private function createDeleteNewsletterCampaignOfferRepositoryInterfaceWithMethodsMock($returns)
    {
        $newsletterCampaignOfferRepository = $this->getMock('\Core\Domain\Repository\NewsletterCampaignOfferRepositoryInterface');
        $newsletterCampaignOfferRepository
            ->expects($this->once())
            ->method('deleteNewsletterCampaignOffers')
            ->willReturn($returns);
        return $newsletterCampaignOfferRepository;
    }
}
