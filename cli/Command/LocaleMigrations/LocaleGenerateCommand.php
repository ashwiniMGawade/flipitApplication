<?php

namespace Command\LocaleMigrations;

use Command\LocaleMigrations\Helpers\LocaleExecuteCommonLogic;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use \Doctrine\DBAL\Migrations\Tools\Console\Helper as DoctrineHelper;
use Symfony\Component\Console\Input as Input;
use Symfony\Component\Console\Helper as Helper;
use \Doctrine\DBAL\Migrations\Tools\Console\Command as MigrationsCommand;

class LocaleGenerateCommand extends Command
{
    use LocaleExecuteCommonLogic;

    private $originalMigrationName = 'migrations:generate';
    private $commandName = 'Doctrine\DBAL\Migrations\Tools\Console\Command\GenerateCommand';

    protected function configure()
    {
        $this
            ->setName('localeMigrations:generate')
            ->setDescription('Generate a blank migration class.')
            ->addOption('editor-cmd', null, Input\InputOption::VALUE_OPTIONAL, 'Open file with this command upon creation.')
            ->setHelp(<<<EOT
The <info>%command.name%</info> command generates a blank migration class:

    <info>%command.full_name%</info>

You can optionally specify a <comment>--editor-cmd</comment> option to open the generated file in your favorite editor:

    <info>%command.full_name% --editor-cmd=mate</info>
EOT
            );

        parent::configure();
    }

    public function execute(Input\InputInterface $input, OutputInterface $output)
    {
        $this->runCommand($input, $output);
    }
}
