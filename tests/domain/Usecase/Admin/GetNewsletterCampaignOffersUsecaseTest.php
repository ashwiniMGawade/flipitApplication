<?php
namespace Usecase\System;

use \Core\Domain\Entity\NewsletterCampaignOffer;
use \Core\Domain\Service\Purifier;
use \Core\Domain\Usecase\System\GetNewsletterCampaignOffersUsecase;
use \Core\Service\Errors;

class GetNewsletterCampaignOffersUsecaseTest extends \Codeception\TestCase\Test
{
    public function testGetNewsletterCampaignOffersUsecaseReturnsZeroWhenNewsletterCampaignOfferDoesNotExist()
    {
        $expectedNewsletterCampaignOffers = 0;
        $newsletterCampaignOfferRepository = $this->createNewsletterCampaignOffersRepositoryWithFindByMethodMock($expectedNewsletterCampaignOffers);
        $newsletterCampaignOffers = (new GetNewsletterCampaignOffersUsecase($newsletterCampaignOfferRepository, new Purifier(), new Errors()))->execute();
        $this->assertEquals($expectedNewsletterCampaignOffers, $newsletterCampaignOffers);
    }

    public function testGetNewsletterCampaignOffersUsecaseReturnsArrayWhenRecordExist()
    {
        $newsletterCampaignOffer = new NewsletterCampaignOffer();
        $expectedResult = array($newsletterCampaignOffer);
        $newsletterCampaignOfferRepository = $this->createNewsletterCampaignOffersRepositoryWithFindByMethodMock($expectedResult);
        $newsletterCampaignOffers = (new GetNewsletterCampaignOffersUsecase($newsletterCampaignOfferRepository, new Purifier(), new Errors()))->execute();
        $this->assertEquals(count($expectedResult), count($newsletterCampaignOffers));
    }

    public function testGetNewsletterCampaignOffersUsecaseReturnsErrorWhenParametersAreInvalid()
    {
        $conditions = 'invalid';
        $newsletterCampaignOfferRepository = $this->createNewsletterCampaignOfferRepositoryMock();
        $errors = new Errors();
        $errors->setError('Invalid input, unable to find record.');
        $result = (new GetNewsletterCampaignOffersUsecase($newsletterCampaignOfferRepository, new Purifier(), new Errors()))->execute($conditions);
        $this->assertInstanceOf('\Core\Service\Errors', $result);
        $this->assertEquals($errors->getErrorsAll(), $result->getErrorsAll());
    }

    public function testGetNewsletterCampaignOffersUsecaseReturnsArrayOfCampaignssObjectWhenParametersAreValid()
    {
        $newsletterCampaignOfferRepository = $this->createNewsletterCampaignOffersRepositoryInterfaceMockWithPaginatedMethod(array(new NewsletterCampaignOffer()));
        $campaigns = (new GetNewsletterCampaignOffersUsecase($newsletterCampaignOfferRepository, new Purifier(), new Errors()))->execute(array(), array(), null, null, true);
        $this->assertNotEmpty($campaigns);
    }

    private function createNewsletterCampaignOfferRepositoryMock()
    {
        $newsletterCampaignOfferRepository = $this->getMock('\Core\Domain\Repository\NewsletterCampaignOfferRepositoryInterface');
        return $newsletterCampaignOfferRepository;
    }

    private function createNewsletterCampaignOffersRepositoryWithFindByMethodMock($returns)
    {
        $newsletterCampaignOfferRepository = $this->createNewsletterCampaignOfferRepositoryMock();
        $newsletterCampaignOfferRepository->expects($this->once())
            ->method('findBy')
            ->with($this->equalTo('\Core\Domain\Entity\NewsletterCampaignOffer'), $this->isType('array'))
            ->willReturn($returns);
        return $newsletterCampaignOfferRepository;
    }

    private function createNewsletterCampaignOffersRepositoryInterfaceMockWithPaginatedMethod($returns)
    {
        $newsletterCampaignOfferRepository = $this->createNewsletterCampaignOfferRepositoryMock();
        $newsletterCampaignOfferRepository->expects($this->once())
            ->method('findAllPaginated')
            ->with('\Core\Domain\Entity\NewsletterCampaignOffer', $this->isType('array'), $this->isType('array'))
            ->willReturn($returns);
        return $newsletterCampaignOfferRepository;
    }
}
