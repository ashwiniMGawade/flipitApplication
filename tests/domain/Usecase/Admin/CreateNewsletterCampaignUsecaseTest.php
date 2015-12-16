<?php
namespace Usecase\Admin;

use Core\Domain\Usecase\Admin\CreateNewsletterCampaignUsecase;

class CreateNewsletterCampaignUsecaseTest extends \Codeception\TestCase\Test
{
    public function testCreateNewsletterCampaignUsecase()
    {
        $this->assertInstanceOf(
            '\Core\Domain\Entity\NewsletterCampaign',
            (new CreateNewsletterCampaignUsecase())->execute()
        );
    }
}
