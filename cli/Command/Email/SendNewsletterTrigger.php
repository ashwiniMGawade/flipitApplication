<?php
namespace Command\Email;

use Core\Domain\Factory\SystemFactory;
use Core\Service\LocaleLister;
use Core\Domain\Entity\NewsletterCampaign;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SendNewsletterTrigger extends Command
{
    protected function configure()
    {
        $this
            ->setName('email:sendNewsletterTrigger')
            ->setDescription('This command will check whether newsletter is scheduled, if yes then start a new job to send a scheduled newsletter')
            ->setHelp(<<<EOT
The <info>%command.name%</info> command generates an excel files for pay:

<info>%command.full_name%</info>
EOT
            );

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $locales = (new LocaleLister)->getAllLocales();

        $newsletterCampaigns = array();

        foreach ($locales as $locale) {
            $newsletterCampaign = SystemFactory::getNewsletterCampaigns($locale)->execute();
            if (!empty($newsletterCampaign[0]) && $newsletterCampaign[0] instanceof NewsletterCampaign) {
                array_push($newsletterCampaigns, $newsletterCampaign[0]);
            }
        }

        $newsletterCampaignsToSend = $this->_validateToSend($newsletterCampaigns);
        $scheduledNewsletters = $this->_scheduleNewsletter($newsletterCampaignsToSend);
        $output->writeln($scheduledNewsletters);
    }

    private function _validateToSend(array $newsletterCampaigns)
    {
        $currentDateTime = new \DateTime('now', (new \DateTimezone("Europe/Amsterdam")));
        $newsletterCampaignsToSend = array();

        foreach ($newsletterCampaigns as $newsletterCampaign) {
            if ($currentDateTime > $newsletterCampaign->scheduledTime) {
                array_push($newsletterCampaignsToSend, $newsletterCampaign);
            }
        }

        return $newsletterCampaignsToSend;
    }

    private function _scheduleNewsletter($newsletterCampaigns)
    {
        return "I would like to schedule the campaign, but need a DynamoDb connection first...";
    }
}
