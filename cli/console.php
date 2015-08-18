#!/usr/bin/env php
<?php

set_time_limit(0);
require_once __DIR__ . '/../vendor/autoload.php';
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;
use Doctrine\DBAL\Migrations\Tools\Console\Command as MigrationsCommand;
use \Command\GreetCommand;
use \Command\Email\VisitorInactiveCommand;

define('APPLICATION_ENV', 'development');
$cli = new Application();
$cli->setCatchExceptions(true);

$helperSet = new Console\Helper\HelperSet();
$helperSet->set(new Console\Helper\DialogHelper(), 'dialog');
$cli->setHelperSet($helperSet);

$cli->add(new MigrationsCommand\ExecuteCommand());
$cli->add(new MigrationsCommand\GenerateCommand());
$cli->add(new MigrationsCommand\LatestCommand());
$cli->add(new MigrationsCommand\MigrateCommand());
$cli->add(new MigrationsCommand\StatusCommand());
$cli->add(new MigrationsCommand\VersionCommand());
$cli->add(new GreetCommand());
$app->add(new VisitorInactiveCommand());
$cli->run();
