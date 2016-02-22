<?php
namespace Usecase\Admin;

use \Core\Domain\Entity\NewsletterCampaign;
use \Core\Domain\Usecase\Admin\DeleteNewsletterCampaignUsecase;

class DeleteNewsletterCampaignUsecaseTest extends \Codeception\TestCase\Test
{

    public function testDeleteNewsletterCampaignUsecase()
    {
        $newsletterCampaignRepository = $this->createDeleteNewsletterCampaignRepositoryInterfaceWithMethodsMock(true);
        $this->assertEquals(true, (new DeleteNewsletterCampaignUsecase($newsletterCampaignRepository))->execute(new NewsletterCampaign));
    }

    private function createDeleteNewsletterCampaignRepositoryInterfaceWithMethodsMock($returns)
    {
        $newsletterCampaignRepository = $this->getMock('\Core\Domain\Repository\NewsletterCampaignRepositoryInterface');
        $newsletterCampaignRepository
            ->expects($this->once())
            ->method('remove')
            ->willReturn($returns);
        return $newsletterCampaignRepository;
    }
}
