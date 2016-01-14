<?php
namespace Usecase\Admin;

use \Core\Domain\Entity\NewsletterCampaign;
use \Core\Domain\Service\Purifier;
use \Core\Domain\Usecase\Admin\GetNewsletterCampaignUsecase;
use \Core\Service\Errors;

class GetNewsletterCampaignUsecaseTest extends \Codeception\TestCase\Test
{
    public function testGetNewsletterCampaignUsecaseReturnsErrorWhenRecordDoesNotExist()
    {
        $condition = array('id' => 0);
        $newsletterCampaignRepositoryMock = $this->createNewsletterCampaignRepositoryWithFindMethodMock($condition, 0);
        $newsletterCampaignUsecase = new GetNewsletterCampaignUsecase($newsletterCampaignRepositoryMock, new Purifier(), new Errors());
        $result = $newsletterCampaignUsecase->execute($condition);
        $errors = new Errors();
        $errors->setError('Newsletter Campaign not found');
        $this->assertEquals($errors->getErrorsAll(), $result->getErrorsAll());
    }

    public function testGetNewsletterCampaignUsecaseRetunsObjectWhenValidInputPassed()
    {
        $condition = array('id' => 0);
        $newsletterCampaign = new NewsletterCampaign();
        $newsletterCampaign->setId(0);
        $newsletterCampaignRepositoryMock = $this->createNewsletterCampaignRepositoryWithFindMethodMock($condition, $newsletterCampaign);
        $newsletterCampaignUsecase = new GetNewsletterCampaignUsecase($newsletterCampaignRepositoryMock, new Purifier(), new Errors());
        $result = $newsletterCampaignUsecase->execute($condition, false);
        $this->assertEquals($newsletterCampaign, $result);
    }

    public function testGetNewsletterCampaignUsecaseRetunsObjectWithWarningsWhenValidInputPassed()
    {
        $condition = array('id' => 0);
        $newsletterCampaign = new NewsletterCampaign();
        $newsletterCampaign->setId(0);
        $newsletterCampaignRepositoryMock = $this->createNewsletterCampaignRepositoryWithFindMethodMock($condition, $newsletterCampaign);
        $newsletterCampaignUsecase = new GetNewsletterCampaignUsecase($newsletterCampaignRepositoryMock, new Purifier(), new Errors());
        $result = $newsletterCampaignUsecase->execute($condition, true);
        $this->assertEquals($newsletterCampaign, $result);
    }

    public function testGetNewsletterCampaignUsecaseReturnsErrorWhenInvalidInputPassed()
    {
        $condition = 'invalid';
        $newsletterCampaignRepositoryMock = $this->createNewsletterCampaignRepositoryMock();
        $newsletterCampaignUsecase = new GetNewsletterCampaignUsecase($newsletterCampaignRepositoryMock, new Purifier(), new Errors());
        $result = $newsletterCampaignUsecase->execute($condition);
        $errors = new Errors();
        $errors->setError('Invalid input, unable to find campaign.');
        $this->assertEquals($errors->getErrorsAll(), $result->getErrorsAll());
    }

    private function createNewsletterCampaignRepositoryMock()
    {
        $newsletterCampaignRepository = $this->getMockBuilder('\Core\Domain\Repository\NewsletterCampaignRepositoryInterface')->getMock();
        return $newsletterCampaignRepository;
    }

    private function createNewsletterCampaignRepositoryWithFindMethodMock($condition, $returns)
    {
        $newsletterCampaignRepositoryMock = $this->createNewsletterCampaignRepositoryMock();
        $newsletterCampaignRepositoryMock
            ->expects($this->once())
            ->method('findOneBy')
            ->with($this->equalTo('\Core\Domain\Entity\NewsletterCampaign'), $this->equalTo($condition))
            ->willReturn($returns);
        return $newsletterCampaignRepositoryMock;
    }
}
