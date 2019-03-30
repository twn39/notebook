<?php

namespace App\Models;

use Illuminate\Database\Capsule\Manager;

class Task
{

    /**
     * @var Manager
     */
    private $DB;

    const TABLE = 'tasks';

    const PENDING = 0;
    const IN_PROGRESS = 1;
    const DONE = 2;
    const ARCHIVE = 3;

    public function __construct(Manager $DB)
    {
        $this->DB = $DB;
    }

}