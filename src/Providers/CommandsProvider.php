<?php
/**
 * Created by PhpStorm.
 * User: weinan
 * Date: 2019/3/27
 * Time: 19:15
 */
namespace App\Providers;

use App\Commands\Show;
use Doctrine\DBAL\Connection;
use Illuminate\Database\Capsule\Manager;
use League\CLImate\CLImate;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class CommandsProvider implements ServiceProviderInterface
{

    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container $pimple A container instance
     */
    public function register(Container $pimple)
    {
        $pimple[Show::class] = function () use ($pimple) {
          return new Show($pimple[CLImate::class], $pimple[Manager::class], $pimple['config']);
        };
    }
}