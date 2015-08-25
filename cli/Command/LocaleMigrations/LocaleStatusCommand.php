<?php

namespace Command\LocaleMigrations;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input as Input;
use \Command\LocaleMigrations\Helpers\LocaleExecuteMethod;

class LocaleStatusCommand extends Command
{
    private $originalMigrationName = 'migrations:status';
    private $commandName = 'Doctrine\DBAL\Migrations\Tools\Console\Command\StatusCommand';

    use LocaleExecuteMethod;

    protected function configure()
    {
        $this
            ->setName('localeMigrations:status')
            ->setDescription('View the status of a set of migrations.')
            ->addOption('show-versions', null, Input\InputOption::VALUE_NONE, 'This will display a list of all available migrations and their status')
            ->setHelp(<<<EOT
The <info>%command.name%</info> command outputs the status of a set of migrations:

    <info>%command.full_name%</info>

You can output a list of all available migrations and their status with <comment>--show-versions</comment>:

    <info>%command.full_name% --show-versions</info>
EOT
            );

        parent::configure();
    }
}
