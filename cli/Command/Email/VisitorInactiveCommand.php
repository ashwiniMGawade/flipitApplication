<?php
namespace Command\Email;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class VisitorInactiveCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('email:inactivateVisitors')
            ->setDescription('Inactivate the Visitors that have not opened the newsletters in the past 3 months');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Helloo');
    }
}
