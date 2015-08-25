<?php

namespace Command\LocaleMigrations\Helpers;

use Core\Service\LocaleLister;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\DBAL\Migrations\OutputWriter;
use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use \Doctrine\DBAL\Migrations\Tools\Console\Helper as DoctrineHelper;
use Symfony\Component\Console\Input as Input;
use Symfony\Component\Console\Helper as Helper;

trait LocaleExecuteMethod
{
    public function execute(Input\InputInterface $input, OutputInterface $output)
    {
        $locales = (new LocaleLister)->getAllLocales();

        foreach ($locales as $locale) {
            $cli = new Application();
            $cli->setCatchExceptions(true);

            $localeConfiguration = new ConfigurationHelper($locale, new OutputWriter);
            $localeConfiguration->buildLocaleConfiguration();
            $connection = $localeConfiguration->getConnection();
            $configuration = $localeConfiguration->getConfiguration();

            $helperSet = new Helper\HelperSet();
            $helperSet->set(new Helper\DialogHelper(), 'dialog');
            $helperSet->set(new ConnectionHelper($connection), 'db');
            $helperSet->set(new DoctrineHelper\ConfigurationHelper($connection, $configuration), 'configuration');
            $cli->setHelperSet($helperSet);

            $cli->add(new $this->commandName());

            $command = $cli->find($this->originalMigrationName);

            $commandOutput = $command->run($input, $output);
            $commandOutputWithoutStatusCode = join("\n", array_slice(explode("\n", $commandOutput), 0, -1));

            $output->writeln($commandOutputWithoutStatusCode);
        }
    }
}
