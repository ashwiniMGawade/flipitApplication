<?php
namespace Usecase\Admin;

use Core\Domain\Entity\NewsletterCampaign;
use Core\Domain\Entity\NewsletterCampaignOffer;
use Core\Domain\Service\Purifier;
use Core\Domain\Service\Validator;
use Core\Domain\Usecase\Admin\AddNewsletterCampaignUsecase;
use Core\Domain\Validator\NewsletterCampaignValidator;
use Core\Service\Errors;

class AddNewsletterCampaignUsecaseTest extends \Codeception\TestCase\Test
{
    protected $tester;

    public function testAddNewsletterCampaignUsecaseReturnsErrorWhenParamsAreEmpty()
    {
        $params = array();
        $newsletterCampaignRepository = $this->NewsletterCampaignRepositoryMock();
        $newsletterCampaignOfferRepository = $this->NewsletterCampaignOfferRepositoryMock();
        $newsletterCampaignValidator = new NewsletterCampaignValidator(new Validator());
        $result = (new AddNewsletterCampaignUsecase(
            $newsletterCampaignRepository,
            $newsletterCampaignOfferRepository,
            $newsletterCampaignValidator,
            new Purifier(),
            new Errors()
        ))->execute(new NewsletterCampaign(), new NewsletterCampaignOffer(), $params);
        $errors = new Errors();
        $errors->setError('Invalid Parameters');
        $this->assertEquals($errors->getErrorMessages(), $result->getErrorMessages());
    }

    public function testAddNewsletterCampaignUsecaseReturnsErrorWhenParamsAreInvalid()
    {
        $params = array(
            'createdAt' => null,
            'senderEmail' => 'adfsdfsdf'
        );
        $newsletterCampaignRepository = $this->newsletterCampaignRepositoryMock();
        $newsletterCampaignOfferRepository = $this->NewsletterCampaignOfferRepositoryMock();
        $newsletterCampaignValidator = $this->createNewsletterCampaignValidatorMock(
            array(
                'createdAt' => 'Please enter value',
                'senderEmail' => 'Please enter valid email ID'
            )
        );
        $result = (new AddNewsletterCampaignUsecase(
            $newsletterCampaignRepository,
            $newsletterCampaignOfferRepository,
            $newsletterCampaignValidator,
            new Purifier(),
            new Errors()
        ))->execute(new NewsletterCampaign(), new NewsletterCampaignOffer(), $params);
        $errors = new Errors();
        $errors->setError('Please enter value', 'createdAt');
        $errors->setError('Please enter valid email ID', 'senderEmail');
        $this->assertEquals($errors->getErrorMessages(), $result['error']->getErrorMessages());
    }

    public function testAddNewsletterCampaignUsecaseWithValidInput()
    {
        $params = array(
            'partOneOffers' => array(1,2),
            'partTwoOffers' => array(1,2),
            'campaignName' => 'test',
            'campaignHeader'=> 'header',
            'campaignFooter'=> 'footer',
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
            'scheduledTime' => new \DateTime('now'),
            'newsletterSentTime' => new \DateTime('now'),
            'receipientCount' => 23,
            'deleted' => 1,
        );

        $newsletterCampaignRepository = $this->newsletterCampaignRepositoryMockWithSaveMethod(new NewsletterCampaign());
        $newsletterCampaignOfferRepository = $this->NewsletterCampaignOfferRepositoryMock();
        $newsletterCampaignValidator = $this->createNewsletterCampaignValidatorMock(true);
        $result = (new AddNewsletterCampaignUsecase(
            $newsletterCampaignRepository,
            $newsletterCampaignOfferRepository,
            $newsletterCampaignValidator,
            new Purifier(),
            new Errors()
        ))->execute(new NewsletterCampaign(), new NewsletterCampaignOffer(), $params);
        $this->assertInstanceOf('\Core\Domain\Entity\NewsletterCampaign', $result);
    }

    private function newsletterCampaignRepositoryMock()
    {
        $newsletterCampaignRepositoryMock = $this->getMock('\Core\Domain\Repository\NewsletterCampaignRepositoryInterface');
        return $newsletterCampaignRepositoryMock;
    }

    private function newsletterCampaignOfferRepositoryMock()
    {
        $newsletterCampaignOfferRepositoryMock = $this->getMock('\Core\Domain\Repository\NewsletterCampaignOfferRepositoryInterface');
        return $newsletterCampaignOfferRepositoryMock;
    }

    private function newsletterCampaignRepositoryMockWithSaveMethod($returns)
    {
        $newsletterCampaignRepositoryMock = $this->NewsletterCampaignRepositoryMock();
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
