<?php
/**
 * Created by PhpStorm.
 * User: weinan
 * Date: 2019/3/27
 * Time: 19:12
 */

use Pimple\Container;
use App\Providers\CommandsProvider;
use App\Providers\ServicesProvider;

$pimple = new Container();
$pimple->register(new CommandsProvider());
$pimple->register(new ServicesProvider());
$container = new \Pimple\Psr11\Container($pimple);

return $container;
