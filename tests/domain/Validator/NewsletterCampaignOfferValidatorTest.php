<?php
namespace Validator;

use \Core\Domain\Entity\NewsletterCampaignOffer;
use \Core\Domain\Validator\NewsletterCampaignOfferValidator;

class NewsletterCampaignOfferValidatorTest extends \Codeception\TestCase\Test
{
    protected $tester;

    public function testNewsletterCampaignOfferValidatorWithValidOutcome()
    {
        $newsletterCampaignOfferValidator = new NewsletterCampaignOfferValidator($this->mockValidatorInterface(true));
        $this->assertTrue($newsletterCampaignOfferValidator->validate(new NewsletterCampaignOffer()));
    }

    public function testNewsletterCampaignOfferValidatorWithInvalidOutcome()
    {
        $newsletterCampaignOfferValidator = new NewsletterCampaignOfferValidator($this->mockValidatorInterface(false));
        $this->assertFalse($newsletterCampaignOfferValidator->validate(new NewsletterCampaignOffer()));
    }

    private function mockValidatorInterface($flag)
    {
        $mockValidatorInterface = $this->getMock('\Core\Domain\Adapter\ValidatorInterface');
        $mockValidatorInterface
            ->expects($this->once())
            ->method('validate')
            ->with($this->isType('object'), $this->isType('array'))
            ->willReturn($flag);
        return $mockValidatorInterface;
    }
}
