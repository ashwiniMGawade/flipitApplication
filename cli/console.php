#!/usr/bin/env php
<?php

set_time_limit(0);
require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper as Helper;
use \Doctrine\DBAL\Migrations\Tools\Console\Command as MigrationsCommand;
use \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use \Command\LocaleMigrations\Helpers\ConfigurationHelper;
use \Doctrine\DBAL\Migrations\Tools\Console\Helper as DoctrineHelper;
use \Core\Persistence\Database\Service as Service;
use \Command\GreetCommand;
use \Command\LocaleMigrations as LocaleMigrations;
use \Command\Email\VisitorInactiveCommand;

define('APPLICATION_ENV', 'development');
$cli = new Application();
$cli->setCatchExceptions(true);

$helperSet = new Helper\HelperSet();
$helperSet->set(new Helper\DialogHelper(), 'dialog');

// Add User DB Migrations commands
$configurationHelper = new ConfigurationHelper();
$configurationHelper->buildUserConfiguration();
$userConnection = $configurationHelper->getConnection();
$userConfiguration = $configurationHelper->getConfiguration();

$helperSet->set(new ConnectionHelper($userConnection), 'db');
$helperSet->set(new DoctrineHelper\ConfigurationHelper($userConnection, $userConfiguration), 'configuration');
$cli->setHelperSet($helperSet);

$commands = array();
$commands[] = new MigrationsCommand\ExecuteCommand();
$commands[] = new MigrationsCommand\GenerateCommand();
$commands[] = new MigrationsCommand\LatestCommand();
$commands[] = new MigrationsCommand\MigrateCommand();
$commands[] = new MigrationsCommand\StatusCommand();
$commands[] = new MigrationsCommand\VersionCommand();
foreach ($commands as $command) {
    $command->setName(str_replace('migrations:', 'userMigrations:', $command->getName()));
}
$cli->addCommands($commands);

// Add Locale DB Migrations commands
$cli->add(new LocaleMigrations\LocaleGenerateCommand());
$cli->add(new LocaleMigrations\LocaleExecuteCommand());
$cli->add(new LocaleMigrations\LocaleLatestCommand());
$cli->add(new LocaleMigrations\LocaleMigrateCommand());
$cli->add(new LocaleMigrations\LocaleStatusCommand());
$cli->add(new LocaleMigrations\LocaleVersionCommand());

// Add business logic commands
$cli->add(new GreetCommand());
$cli->add(new VisitorInactiveCommand());

$cli->run();
