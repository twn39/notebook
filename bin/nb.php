<?php

require __DIR__.'/../vendor/autoload.php';

use App\Commands\Show;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\CommandLoader\ContainerCommandLoader;


$container = require __DIR__.'/../src/container.php';
$commands = require __DIR__.'/../src/commands.php';
$commandLoader = new ContainerCommandLoader($container, $commands);

$application = new Application();
$application->setCommandLoader($commandLoader);
$application->setDefaultCommand(Show::getDefaultName());
$application->run();
