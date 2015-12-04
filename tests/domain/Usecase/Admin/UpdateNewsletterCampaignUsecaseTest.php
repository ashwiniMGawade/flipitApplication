<?php
namespace Usecase\Admin;

use Core\Domain\Entity\NewsletterCampaign;
use Core\Domain\Service\Purifier;
use Core\Domain\Service\Validator;
use Core\Domain\Usecase\Admin\UpdateNewsletterCampaignUsecase;
use Core\Domain\Validator\NewsletterCampaignValidator;
use Core\Service\Errors;

class UpdateNewsletterCampaignUsecaseTest extends \Codeception\TestCase\Test
{
    protected $tester;

    public function testUpdateNewsletterCampaignUsecaseReturnsErrorWhenParamsAreEmpty()
    {
        $params = array();
        $newsletterCampaignRepository = $this->NewsletterCampaignRepositoryMock();
        $newsletterCampaignValidator = new NewsletterCampaignValidator(new Validator());
        $result = (new UpdateNewsletterCampaignUsecase(
            $newsletterCampaignRepository,
            $newsletterCampaignValidator,
            new Purifier(),
            new Errors()
        ))->execute(new NewsletterCampaign(), $params);
        $errors = new Errors();
        $errors->setError('Invalid Parameters');
        $this->assertEquals($errors->getErrorMessages(), $result->getErrorMessages());
    }

    public function testUpdateNewsletterCampaignUsecaseReturnsErrorWhenParamsAreInvalid()
    {
        $params = array(
            'scheduledTime' => '1232',
            'createdAt' => null,
            'senderEmail' => 'adfsdfsdf'
        );
        $newsletterCampaignRepository = $this->newsletterCampaignRepositoryMock();
        $newsletterCampaignValidator = $this->createNewsletterCampaignValidatorMock(
            array(
                'scheduledTime' => 'Please enter valid scheduled time',
                'createdAt' => 'Please enter value',
                'senderEmail' => 'Please enter valid email ID'
            )
        );
        $result = (new UpdateNewsletterCampaignUsecase(
            $newsletterCampaignRepository,
            $newsletterCampaignValidator,
            new Purifier(),
            new Errors()
        ))->execute(new NewsletterCampaign(), $params);
        $errors = new Errors();
        $errors->setError('Please enter valid scheduled time', 'scheduledTime');
        $errors->setError('Please enter value', 'createdAt');
        $errors->setError('Please enter valid email ID', 'senderEmail');
        $this->assertEquals($errors->getErrorMessages(), $result->getErrorMessages());
    }

    public function testUpdateNewsletterCampaignUsecaseWithValidInput()
    {
        $params = array(
            'campaignName' => 'test',
            'header'=> 'header',
            'footer'=> 'footer',
            'campaignSubject' => 'test',
            'senderName' => 'test',
            'headerBanner' => 'test',
            'headerBannerURL' => 'test',
            'footerBanner' => 'test',
            'footerBannerURL' => 'test',
            'offerPartOneTitle' => 'test',
            'offerPartTwoTitle' => 'test',
            'senderEmail' => 'test',
            'scheduledStatus' => 1,
            'newsletterSentTime' => new \DateTime('now'),
            'receipientCount' => 23,
            'deleted' => 1,
            'updatedAt' => new \DateTime('now'),
        );

        $newsletterCampaignRepository = $this->newsletterCampaignRepositoryMockWithSaveMethod(new NewsletterCampaign());
        $newsletterCampaignValidator = $this->createNewsletterCampaignValidatorMock(true);
        $result = (new UpdateNewsletterCampaignUsecase(
            $newsletterCampaignRepository,
            $newsletterCampaignValidator,
            new Purifier(),
            new Errors()
        ))->execute(new NewsletterCampaign(), $params);
        $this->assertInstanceOf('\Core\Domain\Entity\NewsletterCampaign', $result);
    }

    private function newsletterCampaignRepositoryMock()
    {
        $newsletterCampaignRepositoryMock = $this->getMock('\Core\Domain\Repository\NewsletterCampaignRepositoryInterface');
        return $newsletterCampaignRepositoryMock;
    }

    private function newsletterCampaignRepositoryMockWithSaveMethod($returns)
    {
        $newsletterCampaignRepositoryMock = $this->newsletterCampaignRepositoryMock();
        $newsletterCampaignRepositoryMock
            ->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf('\Core\Domain\Entity\NewsletterCampaign'))
            ->willReturn($returns);
        return $newsletterCampaignRepositoryMock;
    }

    private function createNewsletterCampaignValidatorMock($returns)
    {
        $mockNewsletterCampaignValidator = $this->getMockBuilder('\Core\Domain\Validator\NewsletterCampaignValidator')
            ->disableOriginalConstructor()
            ->getMock();
        $mockNewsletterCampaignValidator->expects($this->once())
            ->method('validate')
            ->with($this->isInstanceOf('\Core\Domain\Entity\NewsletterCampaign'))
            ->willReturn($returns);
        return $mockNewsletterCampaignValidator;
    }
}
