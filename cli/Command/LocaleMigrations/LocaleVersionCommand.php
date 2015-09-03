<?php

namespace Command\LocaleMigrations;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input as Input;
use \Command\LocaleMigrations\Helpers\LocaleExecuteMethod;

class LocaleVersionCommand extends Command
{
    private $originalMigrationName = 'migrations:version';
    private $commandName = 'Doctrine\DBAL\Migrations\Tools\Console\Command\VersionCommand';

    use LocaleExecuteMethod;

    protected function configure()
    {
        $this
            ->setName('localeMigrations:version')
            ->setDescription('Manually add and delete migration versions from the version table.')
            ->addArgument('version', Input\InputArgument::OPTIONAL, 'The version to add or delete.', null)
            ->addOption('add', null, Input\InputOption::VALUE_NONE, 'Add the specified version.')
            ->addOption('delete', null, Input\InputOption::VALUE_NONE, 'Delete the specified version.')
            ->addOption('all', null, Input\InputOption::VALUE_NONE, 'Apply to all the versions.')
            ->addOption('range-from', null, Input\InputOption::VALUE_OPTIONAL, 'Apply from specified version.')
            ->addOption('range-to', null, Input\InputOption::VALUE_OPTIONAL, 'Apply to specified version.')
            ->setHelp(<<<EOT
The <info>%command.name%</info> command allows you to manually add, delete or synchronize migration versions from the version table:

    <info>%command.full_name% YYYYMMDDHHMMSS --add</info>

If you want to delete a version you can use the <comment>--delete</comment> option:

    <info>%command.full_name% YYYYMMDDHHMMSS --delete</info>

If you want to synchronize by adding or deleting all migration versions available in the version table you can use the <comment>--all</comment> option:

    <info>%command.full_name% --add --all</info>
    <info>%command.full_name% --delete --all</info>

If you want to synchronize by adding or deleting some range of migration versions available in the version table you can use the <comment>--range-from/--range-to</comment> option:

    <info>%command.full_name% --add --range-from=YYYYMMDDHHMMSS --range-to=YYYYMMDDHHMMSS</info>
    <info>%command.full_name% --delete --range-from=YYYYMMDDHHMMSS --range-to=YYYYMMDDHHMMSS</info>

You can also execute this command without a warning message which you need to interact with:

    <info>%command.full_name% --no-interaction</info>
EOT
            );

        parent::configure();
    }
}
