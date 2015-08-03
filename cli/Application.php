#!/usr/bin/env php
<?php
set_time_limit(0);
require_once __DIR__ . '/../vendor/autoload.php';
use Symfony\Component\Console\Application;
use \Command\GreetCommand;
use \Command\Email\VisitorInactiveCommand;

$app = new Application();
$app->add(new GreetCommand());
$app->add(new VisitorInactiveCommand());
$app->run();
