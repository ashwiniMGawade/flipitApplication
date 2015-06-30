<?php

require_once __DIR__ . '/vendor/autoload.php';
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

$application = new Application();
$application->add(new GreetCommand());

$command = $application->find('demo:greet');
$commandTester = new CommandTester($command);
$commandTester->execute(array(
    'command'      => $command->getName()
));
