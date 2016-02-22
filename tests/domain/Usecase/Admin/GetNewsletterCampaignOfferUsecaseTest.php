<?php
namespace Usecase\Admin;

use \Core\Domain\Entity\NewsletterCampaignOffer;
use \Core\Domain\Service\Purifier;
use \Core\Domain\Usecase\Admin\GetNewsletterCampaignOfferUsecase;
use \Core\Service\Errors;

class GetNewsletterCampaignOfferUsecaseTest extends \Codeception\TestCase\Test
{
    public function testGetNewsletterCampaignOfferUsecaseReturnsErrorWhenRecordDoesNotExist()
    {
        $condition = array('id' => 0);
        $newsletterCampaignOfferRepositoryMock = $this->createNewsletterCampaignOfferRepositoryWithFindMethodMock($condition, 0);
        $newsletterCampaignOfferUsecase = new GetNewsletterCampaignOfferUsecase($newsletterCampaignOfferRepositoryMock, new Purifier(), new Errors());
        $result = $newsletterCampaignOfferUsecase->execute($condition);
        $errors = new Errors();
        $errors->setError('Newsletter Campaign offer not found');
        $this->assertEquals($errors->getErrorsAll(), $result->getErrorsAll());
    }

    public function testGetNewsletterCampaignOfferUsecaseRetunsObjectWhenValidInputPassed()
    {
        $condition = array('id' => 0);
        $newsletterCampaignOffer = new NewsletterCampaignOffer();
        $newsletterCampaignOffer->setId(0);
        $newsletterCampaignOfferRepositoryMock = $this->createNewsletterCampaignOfferRepositoryWithFindMethodMock($condition, $newsletterCampaignOffer);
        $newsletterCampaignOfferUsecase = new GetNewsletterCampaignOfferUsecase($newsletterCampaignOfferRepositoryMock, new Purifier(), new Errors());
        $result = $newsletterCampaignOfferUsecase->execute($condition);
        $this->assertEquals($newsletterCampaignOffer, $result);
    }

    public function testGetNewsletterCampaignOfferUsecaseReturnsErrorWhenInvalidInputPassed()
    {
        $condition = 'invalid';
        $newsletterCampaignOfferRepositoryMock = $this->createNewsletterCampaignOfferRepositoryMock();
        $newsletterCampaignOfferUsecase = new GetNewsletterCampaignOfferUsecase($newsletterCampaignOfferRepositoryMock, new Purifier(), new Errors());
        $result = $newsletterCampaignOfferUsecase->execute($condition);
        $errors = new Errors();
        $errors->setError('Invalid input, unable to find campaign.');
        $this->assertEquals($errors->getErrorsAll(), $result->getErrorsAll());
    }

    private function createNewsletterCampaignOfferRepositoryMock()
    {
        $newsletterCampaignOfferRepository = $this->getMockBuilder('\Core\Domain\Repository\NewsletterCampaignOfferRepositoryInterface')->getMock();
        return $newsletterCampaignOfferRepository;
    }

    private function createNewsletterCampaignOfferRepositoryWithFindMethodMock($condition, $returns)
    {
        $newsletterCampaignOfferRepositoryMock = $this->createNewsletterCampaignOfferRepositoryMock();
        $newsletterCampaignOfferRepositoryMock
            ->expects($this->once())
            ->method('findOneBy')
            ->with($this->equalTo('\Core\Domain\Entity\NewsletterCampaignOffer'), $this->equalTo($condition))
            ->willReturn($returns);
        return $newsletterCampaignOfferRepositoryMock;
    }
}
