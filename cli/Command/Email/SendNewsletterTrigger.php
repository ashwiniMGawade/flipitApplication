<?php
namespace Command\Email;

use Core\Domain\Factory\SystemFactory;
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
        $output->writeln('Schedule newsletter.');
    }
}
