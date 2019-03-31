<?php

namespace App\Commands;

use Illuminate\Database\Capsule\Manager;
use League\CLImate\CLImate;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Task extends Command
{
    protected static $defaultName = 'task';
    /**
     * @var CLImate
     */
    private $CLImate;
    /**
     * @var Manager
     */
    private $DB;


    /**
     * Task constructor.
     * @param CLImate $CLImate
     * @param Manager $DB
     */
    public function __construct(CLImate $CLImate, Manager $DB)
    {
        parent::__construct();
        $this->CLImate = $CLImate;
        $this->DB = $DB;
    }

    public function configure()
    {
        $this->setName('task')
            ->setDescription('Task behave.')
            ->setDefinition(
                new InputDefinition([
                    new InputOption('title', 't', InputOption::VALUE_OPTIONAL, 'create a task'),
                    new InputOption('board', 'b', InputOption::VALUE_OPTIONAL, 'special a board', 'Board'),
                    new InputOption('check', 'c', InputOption::VALUE_OPTIONAL, 'check a task'),
                    new InputOption('start', 's', InputOption::VALUE_OPTIONAL, 'start a task'),
                    new InputOption('archive', 'a', InputOption::VALUE_OPTIONAL, 'archive a task'),
                ])
            );
    }


    public function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getOption('title');
        $boardName = $input->getOption('board');
        $checkId = $input->getOption('check');
        $startId = $input->getOption('start');
        $archiveId = $input->getOption('archive');

        $board = $this->DB->table('boards')
            ->where('name', $boardName)
            ->first();

        if (empty($board)) {
            $boardId = $this->createBoard($boardName);
        } else {
            $boardId = $board->id;
        }

        if (is_numeric($checkId)) {
            // 只有-c选项
            $this->check($checkId);
            return ;
        }
        if (is_numeric($startId)) {
            $this->start($startId);
            return ;
        }
        if (is_numeric($archiveId)) {
            $this->archive($archiveId);
            return ;
        }
        if (!empty($name) && empty($checkId) && empty($startId)) {
            // 只有-t选项

            $this->DB->table('tasks')
                ->insert([
                    'title' => $name,
                    'board_id' => $boardId,
                    'status' => 0,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

            $this->CLImate->br()->output("<green>    ✔ Task has created.</green>");
            return ;
        }

        $this->CLImate->br()->output("<green>    ! Invalid options.</green>");
    }

    private function start($taskId)
    {
        $this->DB->table('tasks')
            ->where('id', $taskId)
            ->update([
                'status' => \App\Models\Task::IN_PROGRESS,
            ]);

        $this->CLImate->br()->output("<green>    ✔ Task has start.</green>");
    }

    private function archive($taskId)
    {
        $this->DB->table('tasks')
            ->where('id', $taskId)
            ->update([
                'status' => \App\Models\Task::ARCHIVE,
            ]);
        $this->CLImate->br()->output("<green>    ✔ Task has archived.</green>");
    }

    private function check($taskId)
    {
        $task = $this->DB->table('tasks')
            ->where('id', $taskId)
            ->first();
        if ($task->status == \App\Models\Task::PENDING || $task->status == \App\Models\Task::IN_PROGRESS) {
            $this->DB->table('tasks')
                ->where('id', $taskId)
                ->update([
                    'status' => \App\Models\Task::DONE,
                ]);

        }
        if ($task->status == \App\Models\Task::DONE) {
            $this->DB->table('tasks')
                ->where('id', $taskId)
                ->update([
                    'status' => \App\Models\Task::PENDING,
                ]);
        }

        $this->CLImate->br()->output("<green>    ✔ Task has checked.</green>");
    }


    private function createBoard($name)
    {
        return $this->DB->table('boards')
            ->insertGetId([
                'name' => $name,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
    }
}
