<?php

namespace Command\LocaleMigrations;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input as Input;
use \Command\LocaleMigrations\Helpers\LocaleExecuteMethod;

class LocaleLatestCommand extends Command
{
    private $originalMigrationName = 'migrations:latest';
    private $commandName = 'Doctrine\DBAL\Migrations\Tools\Console\Command\LatestCommand';

    use LocaleExecuteMethod;

    protected function configure()
    {
        $this
            ->setName('localeMigrations:latest')
            ->setDescription('Outputs the latest version number')
        ;

        parent::configure();
    }
}
