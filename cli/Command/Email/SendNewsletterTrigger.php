<?php
namespace Command\Email;

use Core\Domain\Factory\SystemFactory;
use Core\Persistence\Factory\RepositoryFactory;
use Core\Service\LocaleLister;
use Core\Domain\Entity\NewsletterCampaign;
use Core\Domain\Entity\BulkEmail;
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
        $locals = (new LocaleLister)->getAllLocals();

        $newsletterCampaignsToSend = array();

        foreach ($locals as $local) {
            $conditions = array('scheduledStatus' => 1);
            $currentDateTime = new \DateTime('now', (new \DateTimezone("Europe/Amsterdam")));
            $newsletterCampaignsData = SystemFactory::getNewsletterCampaigns($local)->execute($conditions);
            foreach ($newsletterCampaignsData as $newsletterCampaign) {
                if ($newsletterCampaign instanceof NewsletterCampaign && $currentDateTime > $newsletterCampaign->scheduledTime) {
                    $newsletterCampaignsToSend[$local][] = $newsletterCampaign;
                }
            }
        }
        $scheduledNewsletters = $this->_scheduleNewsletter($newsletterCampaignsToSend);
        $output->writeln('<info>' . $scheduledNewsletters . '</info>');
    }

    private function _scheduleNewsletter($newsletterCampaigns)
    {
        $bulkEmailRepository = RepositoryFactory::bulkEmail();
        $result = array();

        foreach ($newsletterCampaigns as $local => $newsletterCampaigns) {
            foreach ($newsletterCampaigns as $newsletterCampaign) {
                $bulkEmail = new BulkEmail;
                $bulkEmail->setEmailType('newsletter');
                $bulkEmail->setLocal($local);
                $bulkEmail->setReferenceId($newsletterCampaign->getId());

                // Creating a new document in object store
                $bulkEmailRepository->save($bulkEmail);

                // Setting the newsletter campaign to scheduled
                $newsletterCampaign->setScheduledStatus(1);

                $newsletterCampaignRepository = RepositoryFactory::newsletterCampaign($local);
                $newsletterCampaignRepository->save($newsletterCampaign);

                array_push($result, $local);
            }
        }

        $scheduledLocales = join(' ', $result);
        return (empty($scheduledLocales)) ?
            "No newsletters scheduled." :
            "For the following local(s) a newsletter is scheduled for sending: " . strtoupper($scheduledLocales);
    }
}
