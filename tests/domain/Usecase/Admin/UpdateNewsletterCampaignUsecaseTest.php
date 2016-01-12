<?php
namespace Usecase\Admin;

use Core\Domain\Entity\NewsletterCampaign;
use Core\Domain\Entity\NewsletterCampaignOffer;
use Core\Domain\Service\Purifier;
use Core\Domain\Service\Validator;
use Core\Domain\Usecase\Admin\UpdateNewsletterCampaignUsecase;
use Core\Domain\Validator\NewsletterCampaignValidator;
use Core\Service\Errors;

class UpdateNewsletterCampaignUsecaseTest extends \Codeception\TestCase\Test
{
    protected $tester;

    public function testUpdateNewsletterCampaignUsecaseWithValidOffersInput()
    {
        $params = array(
            'partOneOffers' => array(1,2),
            'partTwoOffers' => array(1,2),
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
            'newsletterSentTime' => new \DateTime('now'),
            'receipientCount' => 23,
            'deleted' => 1,
            'updatedAt' => new \DateTime('now'),
            'offer' => new NewsletterCampaignOffer(),
        );

        $newsletterCampaignRepository = $this->newsletterCampaignRepositoryMockWithSaveMethod(new NewsletterCampaign());
        $newsletterCampaignOfferRepository = $this->NewsletterCampaignOfferRepositoryMock();
        $newsletterCampaignValidator = $this->createNewsletterCampaignValidatorMock(true);
        $newsletterCampaign = new NewsletterCampaign();
        $newsletterCampaignOffer = new NewsletterCampaignOffer();
        $newsletterCampaignOffer->setSection(1);
        $newsletterCampaign->setNewsletterCampaignOffers(array($newsletterCampaignOffer));
        $newsletterCampaignOffer2 = new NewsletterCampaignOffer();
        $newsletterCampaignOffer2->setSection(2);
        $newsletterCampaign->setNewsletterCampaignOffers($newsletterCampaignOffer);

        $result = (new UpdateNewsletterCampaignUsecase(
            $newsletterCampaignRepository,
            $newsletterCampaignOfferRepository,
            $newsletterCampaignValidator,
            new Purifier(),
            new Errors()
        ))->execute($newsletterCampaign, new NewsletterCampaignOffer(), $params);
        $this->assertInstanceOf('\Core\Domain\Entity\NewsletterCampaign', $result);
    }

    public function testUpdateNewsletterCampaignUsecaseReturnsErrorWhenParamsAreEmpty()
    {
        $params = array();
        $newsletterCampaignRepository = $this->NewsletterCampaignRepositoryMock();
        $newsletterCampaignOfferRepository = $this->NewsletterCampaignOfferRepositoryMock();
        $newsletterCampaignValidator = new NewsletterCampaignValidator(new Validator());
        $result = (new UpdateNewsletterCampaignUsecase(
            $newsletterCampaignRepository,
            $newsletterCampaignOfferRepository,
            $newsletterCampaignValidator,
            new Purifier(),
            new Errors()
        ))->execute(new NewsletterCampaign(), new NewsletterCampaignOffer(), $params);
        $errors = new Errors();
        $errors->setError('Invalid Parameters');
        $this->assertEquals($errors->getErrorMessages(), $result['error']->getErrorMessages());
    }

    public function testUpdateNewsletterCampaignUsecaseReturnsErrorWhenParamsAreInvalid()
    {
        $params = array(
            'scheduledTime' => '1232',
            'createdAt' => null,
            'senderEmail' => 'adfsdfsdf'
        );
        $newsletterCampaignRepository = $this->newsletterCampaignRepositoryMock();
        $newsletterCampaignOfferRepository = $this->newsletterCampaignOfferRepositoryMock();
        $newsletterCampaignValidator = $this->createNewsletterCampaignValidatorMock(
            array(
                'scheduledTime' => 'Please enter valid scheduled time',
                'createdAt' => 'Please enter value',
                'senderEmail' => 'Please enter valid email ID'
            )
        );
        $result = (new UpdateNewsletterCampaignUsecase(
            $newsletterCampaignRepository,
            $newsletterCampaignOfferRepository,
            $newsletterCampaignValidator,
            new Purifier(),
            new Errors()
        ))->execute(new NewsletterCampaign(), new NewsletterCampaignOffer(), $params);
        $errors = new Errors();
        $errors->setError('Please enter valid scheduled time', 'scheduledTime');
        $errors->setError('Please enter value', 'createdAt');
        $errors->setError('Please enter valid email ID', 'senderEmail');
        $this->assertEquals($errors->getErrorMessages(), $result['error']->getErrorMessages());
    }



    public function testUpdateNewsletterCampaignUsecaseWithValidInput()
    {
        $params = array(
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
            'newsletterSentTime' => new \DateTime('now'),
            'receipientCount' => 23,
            'deleted' => 1,
            'updatedAt' => new \DateTime('now'),
            'offer' => new NewsletterCampaignOffer() );

        $newsletterCampaignRepository = $this->newsletterCampaignRepositoryMockWithSaveMethod(new NewsletterCampaign());
        $newsletterCampaignOfferRepository = $this->NewsletterCampaignOfferRepositoryMock();
        $newsletterCampaignValidator = $this->createNewsletterCampaignValidatorMock(true);
        $result = (new UpdateNewsletterCampaignUsecase(
            $newsletterCampaignRepository,
            $newsletterCampaignOfferRepository,
            $newsletterCampaignValidator,
            new Purifier(),
            new Errors()
        ))->execute(new NewsletterCampaign(), new NewsletterCampaignOffer(), $params);
        $this->assertInstanceOf('\Core\Domain\Entity\NewsletterCampaign', $result);
    }



    public function testUpdateNewsletterCampaignUsecaseWithInvalidValidOffersInput()
    {
        $params = array(
            'partOneOffers' => array(1,2),
            'partTwoOffers' => array(1,2),
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
            'newsletterSentTime' => new \DateTime('now'),
            'receipientCount' => 23,
            'deleted' => 1,
            'updatedAt' => new \DateTime('now'),
        );

        $newsletterCampaignRepository = $this->newsletterCampaignRepositoryMockWithSaveMethod(new NewsletterCampaign());
        $newsletterCampaignOfferRepository = $this->NewsletterCampaignOfferRepositoryMock();
        $newsletterCampaignValidator = $this->createNewsletterCampaignValidatorMock(true);
        $result = (new UpdateNewsletterCampaignUsecase(
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
