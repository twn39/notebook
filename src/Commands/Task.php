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
                    new InputOption('title', 't', InputOption::VALUE_REQUIRED),
                    new InputOption('board', 'b', InputOption::VALUE_OPTIONAL, 'special a board', 'My Board'),
                ])
            );
    }


    public function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getOption('title');
        $boardName = $input->getOption('board');

        $board = $this->DB->table('boards')
            ->where('name', $boardName)
            ->first();

        if (empty($board)) {
            $boardId = $this->createBoard($boardName);
        } else {
            $boardId = $board->id;
        }

        $this->DB->table('tasks')
            ->insert([
                'title' => $name,
                'board_id' => $boardId,
                'status' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        $this->CLImate->br()->output("<green>    ✔ Task has created.</green>");
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
