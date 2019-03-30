<?php
/**
 * Created by PhpStorm.
 * User: weinan
 * Date: 2019/3/27
 * Time: 19:20
 */
namespace App\Commands;

use App\Models\Task;
use League\CLImate\CLImate;
use Illuminate\Database\Capsule\Manager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Show extends Command
{
    protected static $defaultName = 'show';
    /**
     * @var CLImate
     */
    private $CLImate;


    private $DB;

    public function __construct(CLImate $CLImate, Manager $DB, $config)
    {
        parent::__construct();
        $this->CLImate = $CLImate;
        $this->DB = $DB;
    }

    protected function configure()
    {
        $this->setDescription("Show the board")
            ->setHelp('View the board');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->listBoard();
        $this->listStats();

        /*
        $this->CLImate->br()->output("    <underline>My Board</underline> <dark_gray>[1/3]</dark_gray>");
        $this->CLImate->output("      <dark_gray>104. </dark_gray><magenta>[ ]</magenta> hello <dark_gray>3d</dark_gray>");
        $this->CLImate->output("      <dark_gray>2. </dark_gray><green> ✔ </green> <dark_gray>完成</dark_gray>");
        $this->CLImate->output("      <dark_gray>3. </dark_gray>[ ] hello");
        $this->CLImate->output("      <dark_gray>4. </dark_gray>[ ] hello");
        $this->CLImate->output("      <dark_gray>5. </dark_gray>[ ] hello");

        $this->CLImate->br()->output("    <yellow>30%</yellow> <dark_gray>of all tasks complete.</dark_gray>");
        $this->CLImate->output("    <green>2</green> <dark_gray>done · </dark_gray><blue>0</blue> <dark_gray>in-progress · </dark_gray><magenta>1</magenta> <dark_gray>pending · </dark_gray><cyan>2</cyan> <dark_gray>notes</dark_gray>");
        */
    }


    public function listBoard()
    {

        $boards = $this->DB->table('boards')
            ->select('*')
            ->get();

        foreach ($boards as $board) {
            $this->CLImate->br()->output("    <underline>{$board->name}</underline> <dark_gray>[1/3]</dark_gray>");
            $tasks = $this->DB->table('tasks')
                ->select('*')
                ->where('board_id', $board->id)
                ->whereIn('status', [0, 1, 2])
                ->orderBy('id')
                ->orderByDesc('priority')
                ->get();

            foreach ($tasks as $task) {
                if ((int)$task->status === Task::PENDING) {
                    $this->CLImate->output("      <dark_gray>{$task->id}. </dark_gray><magenta>[ ]</magenta> {$task->title} <dark_gray>3d</dark_gray>");
                }

                if ((int)$task->status === Task::DONE) {
                    $this->CLImate->output("      <dark_gray>{$task->id}. </dark_gray><green> ✔ </green> <dark_gray>{$task->title}</dark_gray>");
                }
                if ((int)$task->status === Task::IN_PROGRESS) {
                    $this->CLImate->output("      <dark_gray>{$task->id}. </dark_gray><blue> ✦ </blue> <blue>{$task->title}</blue>");
                }

            }
        }
    }


    public function listStats()
    {
        $this->CLImate->br()->output("    <yellow>30%</yellow> <dark_gray>of all tasks complete.</dark_gray>");
        $this->CLImate->output("    <green>2</green> <dark_gray>done · </dark_gray><blue>0</blue> <dark_gray>in-progress · </dark_gray><magenta>1</magenta> <dark_gray>pending · </dark_gray><cyan>2</cyan> <dark_gray>notes</dark_gray>");
    }
}