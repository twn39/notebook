<?php
/**
 * Created by PhpStorm.
 * User: weinan
 * Date: 2019/3/27
 * Time: 19:15
 */
namespace App\Providers;

use App\Commands\Init;
use App\Commands\Note;
use App\Commands\Show;
use App\Commands\Task;
use Pimple\Container;
use League\CLImate\CLImate;
use Pimple\ServiceProviderInterface;
use Illuminate\Database\Capsule\Manager;

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
        $pimple[Task::class] = function () use ($pimple) {
            return new Task($pimple[CLImate::class], $pimple[Manager::class]);
        };

        $pimple[Init::class] = function () use ($pimple) {
            return new Init($pimple[CLImate::class]);
        };

        $pimple[Note::class] = function () use ($pimple) {
            return new Note($pimple[CLImate::class]);
        };
    }
}