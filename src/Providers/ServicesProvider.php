<?php
/**
 * Created by PhpStorm.
 * User: weinan
 * Date: 2019/3/27
 * Time: 19:15
 */

namespace App\Providers;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Illuminate\Database\Capsule\Manager;
use League\CLImate\CLImate;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Illuminate\Database\Capsule\Manager as Capsule;

class ServicesProvider implements ServiceProviderInterface
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
        $pimple[CLImate::class] = function () {
            return new CLImate();
        };

        $pimple['config'] = function () {
            $defaultConfig = [
                'DB' => [
                    'driver' => 'sqlite',
                    'database' => '~/nb/db.sqlite',
                ],
            ];
            $config = @file_get_contents('~/nb/config.json');
            if ($config) {
                $config = @json_decode($config, true);
            }
            if (empty($config)) {
                $config = [];
            }
            return array_replace($defaultConfig, $config);
        };

        $pimple[Manager::class] = function () {
            $capsule = new Capsule;

            $capsule->addConnection([
                'driver'    => 'sqlite',
                'database'  => __DIR__.'/../../data/db.sqlite',
                'prefix'    => '',
            ]);

            $capsule->setAsGlobal();
            $capsule->bootEloquent();

            return $capsule;
        };
    }
}