<?php
namespace Usecase\Admin;

use \Core\Domain\Entity\NewsletterCampaign;
use \Core\Domain\Service\Purifier;
use \Core\Domain\Usecase\Admin\GetNewsletterCampaignsByConditionsUsecase;
use \Core\Service\Errors;

class GetNewsletterCampaignsUsecaseTest extends \Codeception\TestCase\Test
{
    public function testGetNewsletterCampaignsUsecaseReturnsZeroWhenNewsletterCampaignDoesNotExist()
    {
        $expectedNewsletterCampaigns = 0;
        $newsletterCampaignRepository = $this->createNewsletterCampaignsRepositoryWithFindByConditionsMethodMock($expectedNewsletterCampaigns);
        $newsletterCampaigns = (new GetNewsletterCampaignsByConditionsUsecase($newsletterCampaignRepository, new Purifier(), new Errors()))->execute();
        $this->assertEquals($expectedNewsletterCampaigns, $newsletterCampaigns);
    }

    public function testGetNewsletterCampaignsUsecaseReturnsArrayWhenRecordExist()
    {
        $newsletterCampaign = new NewsletterCampaign();
        $expectedResult = array($newsletterCampaign);
        $newsletterCampaignRepository = $this->createNewsletterCampaignsRepositoryWithFindByConditionsMethodMock($expectedResult);
        $newsletterCampaigns = (new GetNewsletterCampaignsByConditionsUsecase($newsletterCampaignRepository, new Purifier(), new Errors()))->execute();
        $this->assertEquals(count($expectedResult), count($newsletterCampaigns));
    }

    public function testGetNewsletterCampaignsUsecaseReturnsErrorWhenParametersAreInvalid()
    {
        $conditions = 'invalid';
        $newsletterCampaignRepository = $this->createNewsletterCampaignRepositoryMock();
        $errors = new Errors();
        $errors->setError('Invalid input, unable to find record.');
        $result = (new GetNewsletterCampaignsByConditionsUsecase($newsletterCampaignRepository, new Purifier(), new Errors()))->execute($conditions);
        $this->assertInstanceOf('\Core\Service\Errors', $result);
        $this->assertEquals($errors->getErrorsAll(), $result->getErrorsAll());
    }

    private function createNewsletterCampaignRepositoryMock()
    {
        $newsletterCampaignRepository = $this->getMock('\Core\Domain\Repository\NewsletterCampaignRepositoryInterface');
        return $newsletterCampaignRepository;
    }

    private function createNewsletterCampaignsRepositoryWithFindByConditionsMethodMock($returns)
    {
        $newsletterCampaignRepository = $this->createNewsletterCampaignRepositoryMock();
        $newsletterCampaignRepository->expects($this->once())
            ->method('findByConditions')
            ->with($this->equalTo('\Core\Domain\Entity\NewsletterCampaign'), $this->isType('array'))
            ->willReturn($returns);
        return $newsletterCampaignRepository;
    }
}
