<?php

namespace Command\LocaleMigrations;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input as Input;
use \Command\LocaleMigrations\Helpers\LocaleExecuteMethod;

class LocaleMigrateCommand extends Command
{
    private $originalMigrationName = 'migrations:migrate';
    private $commandName = 'Doctrine\DBAL\Migrations\Tools\Console\Command\MigrateCommand';

    use LocaleExecuteMethod;

    protected function configure()
    {
        $this
            ->setName('localeMigrations:migrate')
            ->setDescription('Execute a migration to a specified version or the latest available version.')
            ->addArgument('version', Input\InputArgument::OPTIONAL, 'The version number (YYYYMMDDHHMMSS) or alias (first, prev, next, latest) to migrate to.', 'latest')
            ->addOption('write-sql', null, Input\InputOption::VALUE_NONE, 'The path to output the migration SQL file instead of executing it.')
            ->addOption('dry-run', null, Input\InputOption::VALUE_NONE, 'Execute the migration as a dry run.')
            ->addOption('query-time', null, Input\InputOption::VALUE_NONE, 'Time all the queries individually.')
            ->setHelp(<<<EOT
The <info>%command.name%</info> command executes a migration to a specified version or the latest available version:

    <info>%command.full_name%</info>

You can optionally manually specify the version you wish to migrate to:

    <info>%command.full_name% YYYYMMDDHHMMSS</info>

You can specify the version you wish to migrate to using an alias:

    <info>%command.full_name% prev</info>

You can also execute the migration as a <comment>--dry-run</comment>:

    <info>%command.full_name% YYYYMMDDHHMMSS --dry-run</info>

You can output the would be executed SQL statements to a file with <comment>--write-sql</comment>:

    <info>%command.full_name% YYYYMMDDHHMMSS --write-sql</info>

Or you can also execute the migration without a warning message which you need to interact with:

    <info>%command.full_name% --no-interaction</info>

You can also time all the different queries if you wanna know which one is taking so long:

    <info>%command.full_name% --query-time</info>
EOT
            );

        parent::configure();
    }
}
