<?php

namespace Command\LocaleMigrations;

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
        $cli = new Application();
        $cli->setCatchExceptions(true);

        $configurationHelper = new Helpers\ConfigurationHelper();
        $configurationHelper->buildLocaleConfiguration();
        $connection = $configurationHelper->getConnection();
        $configuration = $configurationHelper->getConfiguration();

        $helperSet = new Helper\HelperSet();
        $helperSet->set(new Helper\DialogHelper(), 'dialog');
        $helperSet->set(new ConnectionHelper($connection), 'db');
        $helperSet->set(new DoctrineHelper\ConfigurationHelper($connection, $configuration), 'configuration');
        $cli->setHelperSet($helperSet);

        $cli->add(new MigrationsCommand\GenerateCommand());

        $command = $cli->find('migrations:generate');

        $commandOutput = $command->run($input, $output);
        $commandOutputWithoutStatusCode = join("\n", array_slice(explode("\n", $commandOutput), 0, -1));

        $output->writeln($commandOutputWithoutStatusCode);
    }
}
