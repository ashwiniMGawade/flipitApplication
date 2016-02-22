<?php
namespace Usecase\System;

use \Core\Domain\Entity\NewsletterCampaign;
use \Core\Domain\Service\Purifier;
use \Core\Domain\Usecase\System\GetNewsletterCampaignsUsecase;
use \Core\Service\Errors;

class GetNewsletterCampaignsUsecaseTest extends \Codeception\TestCase\Test
{
    public function testGetNewsletterCampaignsUsecaseReturnsZeroWhenNewsletterCampaignDoesNotExist()
    {
        $expectedNewsletterCampaigns = 0;
        $newsletterCampaignRepository = $this->createNewsletterCampaignsRepositoryWithFindByMethodMock($expectedNewsletterCampaigns);
        $newsletterCampaigns = (new GetNewsletterCampaignsUsecase($newsletterCampaignRepository, new Purifier(), new Errors()))->execute();
        $this->assertEquals($expectedNewsletterCampaigns, $newsletterCampaigns);
    }

    public function testGetNewsletterCampaignsUsecaseReturnsArrayWhenRecordExist()
    {
        $newsletterCampaign = new NewsletterCampaign();
        $expectedResult = array($newsletterCampaign);
        $newsletterCampaignRepository = $this->createNewsletterCampaignsRepositoryWithFindByMethodMock($expectedResult);
        $newsletterCampaigns = (new GetNewsletterCampaignsUsecase($newsletterCampaignRepository, new Purifier(), new Errors()))->execute();
        $this->assertEquals(count($expectedResult), count($newsletterCampaigns));
    }

    public function testGetNewsletterCampaignsUsecaseReturnsErrorWhenParametersAreInvalid()
    {
        $conditions = 'invalid';
        $newsletterCampaignRepository = $this->createNewsletterCampaignRepositoryMock();
        $errors = new Errors();
        $errors->setError('Invalid input, unable to find record.');
        $result = (new GetNewsletterCampaignsUsecase($newsletterCampaignRepository, new Purifier(), new Errors()))->execute($conditions);
        $this->assertInstanceOf('\Core\Service\Errors', $result);
        $this->assertEquals($errors->getErrorsAll(), $result->getErrorsAll());
    }

    public function testGetNewsletterCampaignsUsecaseReturnsArrayOfCampaignssObjectWithWarningsWhenParametersAreValid()
    {
        $newsletterCampaignRepository = $this->createNewsletterCampaignsRepositoryInterfaceMockWithPaginatedMethod(array('records' => array(new NewsletterCampaign())));
        $campaigns = (new GetNewsletterCampaignsUsecase($newsletterCampaignRepository, new Purifier(), new Errors()))->execute(array(), array(), null, null, true, true);
        $this->assertNotEmpty($campaigns);
    }

    public function testGetNewsletterCampaignsUsecaseReturnsArrayOfCampaignssObjectWhenParametersAreValid()
    {
        $newsletterCampaignRepository = $this->createNewsletterCampaignsRepositoryInterfaceMockWithPaginatedMethod(array(new NewsletterCampaign()));
        $campaigns = (new GetNewsletterCampaignsUsecase($newsletterCampaignRepository, new Purifier(), new Errors()))->execute(array(), array(), null, null, true);
        $this->assertNotEmpty($campaigns);
    }

    private function createNewsletterCampaignRepositoryMock()
    {
        $newsletterCampaignRepository = $this->getMock('\Core\Domain\Repository\NewsletterCampaignRepositoryInterface');
        return $newsletterCampaignRepository;
    }

    private function createNewsletterCampaignsRepositoryWithFindByMethodMock($returns)
    {
        $newsletterCampaignRepository = $this->createNewsletterCampaignRepositoryMock();
        $newsletterCampaignRepository->expects($this->once())
            ->method('findBy')
            ->with($this->equalTo('\Core\Domain\Entity\NewsletterCampaign'), $this->isType('array'))
            ->willReturn($returns);
        return $newsletterCampaignRepository;
    }

    private function createNewsletterCampaignsRepositoryInterfaceMockWithPaginatedMethod($returns)
    {
        $newsletterCampaignRepository = $this->createNewsletterCampaignRepositoryMock();
        $newsletterCampaignRepository->expects($this->once())
            ->method('findAllPaginated')
            ->with('\Core\Domain\Entity\NewsletterCampaign', $this->isType('array'), $this->isType('array'))
            ->willReturn($returns);
        return $newsletterCampaignRepository;
    }
}
