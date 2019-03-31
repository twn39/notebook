<?php
/**
 * Created by PhpStorm.
 * User: weinan
 * Date: 2019/3/27
 * Time: 19:01
 */

use App\Commands\Init;
use App\Commands\Show;
use App\Commands\Task;

return [
    'show' => Show::class,
    'task' => Task::class,
    'init' => Init::class,
];