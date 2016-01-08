<?php
namespace Usecase\Admin;

use Core\Domain\Entity\NewsletterCampaignOffer;
use Core\Domain\Entity\NewsletterCampaign;
use Core\Domain\Entity\Offer;
use Core\Domain\Service\Purifier;
use Core\Domain\Service\Validator;
use Core\Domain\Usecase\Admin\AddNewsletterCampaignOfferUsecase;
use Core\Domain\Validator\NewsletterCampaignOfferValidator;
use Core\Service\Errors;

class AddNewsletterCampaignOfferUsecaseTest extends \Codeception\TestCase\Test
{
    protected $tester;

    public function testAddNewsletterCampaignOfferUsecaseReturnsErrorWhenParamsAreEmpty()
    {
        $params = array();
        $newsletterCampaignOfferRepository = $this->NewsletterCampaignOfferRepositoryMock();
        $newsletterCampaignOfferValidator = new NewsletterCampaignOfferValidator(new Validator());
        $result = (new AddNewsletterCampaignOfferUsecase(
            $newsletterCampaignOfferRepository,
            $newsletterCampaignOfferValidator,
            new Purifier(),
            new Errors()
        ))->execute(new NewsletterCampaignOffer(), $params);
        $errors = new Errors();
        $errors->setError('Invalid Parameters');
        $this->assertEquals($errors->getErrorMessages(), $result->getErrorMessages());
    }

    public function testAddNewsletterCampaignOfferUsecaseReturnsErrorWhenParamsAreInvalid()
    {
        $params = array(
            'position' => 'saasdfasd',
            'createdAt' => null
        );
        $newsletterCampaignOfferRepository = $this->newsletterCampaignOfferRepositoryMock();
        $newsletterCampaignOfferValidator = $this->createNewsletterCampaignOfferValidatorMock(
            array(
                'position' => 'Position should be numeric and greater than zero.',
                'createdAt' => 'Please enter Created date'
            )
        );
        $result = (new AddNewsletterCampaignOfferUsecase(
            $newsletterCampaignOfferRepository,
            $newsletterCampaignOfferValidator,
            new Purifier(),
            new Errors()
        ))->execute(new NewsletterCampaignOffer(), $params);
        $errors = new Errors();
        $errors->setError('Position should be numeric and greater than zero.', 'position');
        $errors->setError('Please enter Created date', 'createdAt');
        $this->assertEquals($errors->getErrorMessages(), $result->getErrorMessages());
    }

    public function testAddNewsletterCampaignOfferUsecaseWithValidInput()
    {
        $params = array(
            'newsletterCampaign' => new NewsletterCampaign(),
            'offer'=>  new Offer(),
            'postion'=> '1',
            'section' => '1',
            'deleted' => 1
        );

        $newsletterCampaignOfferRepository = $this->newsletterCampaignOfferRepositoryMockWithAddNewsletterCampaignOfferMethod(new NewsletterCampaignOffer());
        $newsletterCampaignOfferValidator = $this->createNewsletterCampaignOfferValidatorMock(true);
        $result = (new AddNewsletterCampaignOfferUsecase(
            $newsletterCampaignOfferRepository,
            $newsletterCampaignOfferValidator,
            new Purifier(),
            new Errors()
        ))->execute(new NewsletterCampaignOffer(), $params);
        $this->assertInstanceOf('\Core\Domain\Entity\NewsletterCampaignOffer', $result);
    }

    private function newsletterCampaignOfferRepositoryMock()
    {
        $newsletterCampaignOfferRepositoryMock = $this->getMock('\Core\Domain\Repository\NewsletterCampaignOfferRepositoryInterface');
        return $newsletterCampaignOfferRepositoryMock;
    }

    private function newsletterCampaignOfferRepositoryMockWithAddNewsletterCampaignOfferMethod($returns)
    {
        $newsletterCampaignOfferRepositoryMock = $this->NewsletterCampaignOfferRepositoryMock();
        $newsletterCampaignOfferRepositoryMock
            ->expects($this->once())
            ->method('addNewsletterCampaignOffer')
            ->with($this->isInstanceOf('\Core\Domain\Entity\NewsletterCampaignOffer'))
            ->willReturn($returns);
        return $newsletterCampaignOfferRepositoryMock;
    }

    private function createNewsletterCampaignOfferValidatorMock($returns)
    {
        $mockNewsletterCampaignOfferValidator = $this->getMockBuilder('\Core\Domain\Validator\NewsletterCampaignOfferValidator')
            ->disableOriginalConstructor()
            ->getMock();
        $mockNewsletterCampaignOfferValidator->expects($this->once())
            ->method('validate')
            ->with($this->isInstanceOf('\Core\Domain\Entity\NewsletterCampaignOffer'))
            ->willReturn($returns);
        return $mockNewsletterCampaignOfferValidator;
    }
}
