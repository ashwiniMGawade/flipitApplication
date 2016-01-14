<?php
namespace Command\Email;

use \Core\Domain\Factory\SystemFactory;
use \Core\Service\LocaleLister;
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
The <info>%command.name%</info> command will loop all the locale & check whether a newsletter campaign is scheduled & trigger a new process to send newsletter

<info>%command.full_name%</info>
EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $locals = (new localeLister)->getAllLocals();

        $scheduledNewsletterNames = array();

        foreach ($locals as $local) {
            $conditions = array('scheduledStatus' => 1);
            $newsletterCampaigns = SystemFactory::getNewsletterCampaigns($local)->execute($conditions);
            foreach ($newsletterCampaigns as $newsletterCampaign) {
                array_push($scheduledNewsletterNames, SystemFactory::sendNewsletterTrigger($local)->execute($newsletterCampaign));
            }
        }

        $scheduledNewsletters = null;
        foreach ($scheduledNewsletterNames as $scheduledNewsletterName) {
            $scheduledNewsletters .= (!empty($scheduledNewsletterName)) ? "- " . $scheduledNewsletterName . "\n" : "";
        }

        $resultMessage =  (empty($scheduledNewsletters)) ?
            "No newsletters scheduled." :
            "Newsletter(s) scheduled for sending: \n" . $scheduledNewsletters;

        $output->writeln('<info>' . $resultMessage . '</info>');
    }
}
