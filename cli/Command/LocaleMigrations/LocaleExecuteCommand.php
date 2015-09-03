<?php

namespace Command\LocaleMigrations;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input as Input;
use \Command\LocaleMigrations\Helpers\LocaleExecuteMethod;

class LocaleExecuteCommand extends Command
{
    private $originalMigrationName = 'migrations:execute';
    private $commandName = 'Doctrine\DBAL\Migrations\Tools\Console\Command\ExecuteCommand';

    use LocaleExecuteMethod;

    protected function configure()
    {
        $this
            ->setName('localeMigrations:execute')
            ->setDescription('Execute a single migration version up or down manually.')
            ->addArgument('version', Input\InputArgument::REQUIRED, 'The version to execute.', null)
            ->addOption('write-sql', null, Input\InputOption::VALUE_NONE, 'The path to output the migration SQL file instead of executing it.')
            ->addOption('dry-run', null, Input\InputOption::VALUE_NONE, 'Execute the migration as a dry run.')
            ->addOption('up', null, Input\InputOption::VALUE_NONE, 'Execute the migration up.')
            ->addOption('down', null, Input\InputOption::VALUE_NONE, 'Execute the migration down.')
            ->addOption('query-time', null, Input\InputOption::VALUE_NONE, 'Time all the queries individually.')
            ->setHelp(<<<EOT
The <info>%command.name%</info> command executes a single migration version up or down manually:

    <info>%command.full_name% YYYYMMDDHHMMSS</info>

If no <comment>--up</comment> or <comment>--down</comment> option is specified it defaults to up:

    <info>%command.full_name% YYYYMMDDHHMMSS --down</info>

You can also execute the migration as a <comment>--dry-run</comment>:

    <info>%command.full_name% YYYYMMDDHHMMSS --dry-run</info>

You can output the would be executed SQL statements to a file with <comment>--write-sql</comment>:

    <info>%command.full_name% YYYYMMDDHHMMSS --write-sql</info>

Or you can also execute the migration without a warning message which you need to interact with:

    <info>%command.full_name% --no-interaction</info>
EOT
            );

        parent::configure();
    }
}
