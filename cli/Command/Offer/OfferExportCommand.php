<?php
namespace Command\Email;

use Core\Domain\Factory\SystemFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class OfferExportCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('offer:exportAll')
            ->setDescription('Generate .xlsl file for all offers from all locale')
            ->setHelp(<<<EOT
The <info>%command.name%</info> command generates an excel files for pay:

<info>%command.full_name%</info>
EOT
            );

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $numberOfDeactivatedVisitors = SystemFactory::deactivateSleepingVisitors()->execute();
        $output->writeln('Deactivated '. $numberOfDeactivatedVisitors .' Visitors.');
    }
}
