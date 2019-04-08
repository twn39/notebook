<?php
/**
 * Created by PhpStorm.
 * User: weinan
 * Date: 2019/3/27
 * Time: 19:15
 */

namespace App\Providers;

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

        $pimple['config'] = function () use ($pimple) {

            $userProfileDir = $_SERVER['USERPROFILE'] ?? $_SERVER['HOME'];
            $configDir = $userProfileDir.'/.nb';

            $config = @json_decode(file_get_contents($configDir.'/config.json'), true);
            if (empty($config)) {
                $pimple[CLImate::class]->br()->output("    <red>❌  Please run \"init\" command first.</red>");
                exit(0);
            }
            return $config;
        };

        $pimple[Manager::class] = function () use ($pimple) {
            $capsule = new Capsule;

            $capsule->addConnection($pimple['config']['DB']);

            $capsule->setAsGlobal();
            $capsule->bootEloquent();

            return $capsule;
        };
    }
}