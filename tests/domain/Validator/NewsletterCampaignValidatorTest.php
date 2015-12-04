<?php
namespace Validator;

use \Core\Domain\Entity\NewsletterCampaign;
use \Core\Domain\Validator\NewsletterCampaignValidator;

class NewsletterCampaignValidatorTest extends \Codeception\TestCase\Test
{
    protected $tester;

    public function testNewsletterCampaignValidatorWithValidOutcome()
    {
        $newsletterCampaignValidator = new NewsletterCampaignValidator($this->mockValidatorInterface(true));
        $this->assertTrue($newsletterCampaignValidator->validate(new NewsletterCampaign()));
    }

    public function testNewsletterCampaignValidatorWithInvalidOutcome()
    {
        $newsletterCampaignValidator = new NewsletterCampaignValidator($this->mockValidatorInterface(false));
        $this->assertFalse($newsletterCampaignValidator->validate(new NewsletterCampaign()));
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
