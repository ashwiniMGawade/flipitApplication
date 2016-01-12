<?php
namespace Usecase\Helpers;

use Core\Domain\Entity\NewsletterCampaign;

/**
 * Class NewsletterCampaignBuilderTest
 *
 * @package Usecase\Helpers
 */
class NewsletterCampaignBuilderTest extends \Codeception\TestCase\Test
{
    use \Core\Domain\Usecase\Helpers\NewsletterCampaignBuilder;

    /**
     * @var \DomainTester
     */
    protected $tester;

    /**
     * @throws \Exception
     */
    public function testReturnWarningAsTrueOrFalseWhenReturnWarningMessagesIsFalse()
    {
        $this->checkNewsletterForWarnings(new NewsletterCampaign);
    }
}
