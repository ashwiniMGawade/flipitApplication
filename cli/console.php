#!/usr/bin/env php
<?php

set_time_limit(0);
require_once __DIR__ . '/vendor/autoload.php';
use Symfony\Component\Console;
use Doctrine\DBAL\Migrations\Tools\Console\Command as MigrationsCommand;

// Instantiate console application
$cli = new Console\Application();
$cli->setCatchExceptions(true);

$helperSet = new Console\Helper\HelperSet();
$helperSet->set(new Console\Helper\DialogHelper(), 'dialog');
$cli->setHelperSet($helperSet);

// Add Migrations commands
$cli->add(new MigrationsCommand\DiffCommand());
$cli->add(new MigrationsCommand\ExecuteCommand());
$cli->add(new MigrationsCommand\GenerateCommand());
$cli->add(new MigrationsCommand\LatestCommand());
$cli->add(new MigrationsCommand\MigrateCommand());
$cli->add(new MigrationsCommand\StatusCommand());
$cli->add(new MigrationsCommand\VersionCommand());
$cli->add(new GreetCommand());
$cli->run();
