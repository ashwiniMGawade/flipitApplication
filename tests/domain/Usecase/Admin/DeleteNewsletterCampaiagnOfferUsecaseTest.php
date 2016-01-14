<?php
namespace Usecase\Admin;

use \Core\Domain\Usecase\Admin\DeleteNewsletterCampaignOfferUsecase;
use Core\Service\Errors;
use Core\Domain\Service\Purifier;

class DeleteNewsletterCampaignOfferUsecaseTest extends \Codeception\TestCase\Test
{
    public function testDeleteNewsletterCampaignOfferUsecaseReturnsErrorWhenParamsAreEmpty()
    {
        $params = array();
        $newsletterCampaignOfferRepository = $this->newsletterCampaignRepositoryMock();
        $result = (new DeleteNewsletterCampaignOfferUsecase(
            $newsletterCampaignOfferRepository,
            new Purifier(),
            new Errors()
        ))->execute($params);
        $errors = new Errors();
        $errors->setError('Invalid Parameters');
        $this->assertEquals($errors->getErrorMessages(), $result->getErrorMessages());
    }

    public function testDeleteNewsletterCampaignOfferUsecase()
    {
        $newsletterCampaignOfferRepository = $this->createDeleteNewsletterCampaignOfferRepositoryInterfaceWithMethodsMock(true);
        $this->assertEquals(true, (new DeleteNewsletterCampaignOfferUsecase($newsletterCampaignOfferRepository, new Purifier(), new Errors()))->execute([1, 2]));
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

    private function newsletterCampaignRepositoryMock()
    {
        $newsletterCampaignOfferRepository = $this->getMock('\Core\Domain\Repository\NewsletterCampaignOfferRepositoryInterface');
        return $newsletterCampaignOfferRepository;
    }
}
