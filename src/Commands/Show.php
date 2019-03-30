<?php
/**
 * Created by PhpStorm.
 * User: weinan
 * Date: 2019/3/27
 * Time: 19:20
 */
namespace App\Commands;

use App\Models\Task;
use Carbon\Carbon;
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
                $date = '';
                if ((time() - strtotime($task->created_at)) > 1800) {
                    $date = Carbon::createFromTimeString($task->created_at)->diffForHumans();
                }
                if ((int)$task->status === Task::PENDING) {
                    $this->CLImate->output("      <dark_gray>{$task->id}. </dark_gray><magenta>[ ]</magenta> {$task->title} <dark_gray>  {$date}</dark_gray>");
                }

                if ((int)$task->status === Task::DONE) {
                    $this->CLImate->output("      <dark_gray>{$task->id}. </dark_gray><green> ✔ </green> <dark_gray>{$task->title}    {$date}</dark_gray>");
                }
                if ((int)$task->status === Task::IN_PROGRESS) {
                    $this->CLImate->output("      <dark_gray>{$task->id}. </dark_gray><blue> ✦ </blue> <blue>{$task->title}</blue><dark_gray>   {$date}</dark_gray>");
                }

            }
        }
    }


    public function listStats()
    {
        $tasks = $this->DB->table('tasks')
            ->whereIn('status', [0, 1, 2])
            ->get()->toArray();

        $done = [];
        $inProgress = [];
        $pending = [];

        foreach ($tasks as $task) {
            if ($task->status == Task::DONE) {
                $done[] = $task;
            } else if ($task->status == Task::IN_PROGRESS) {
                $inProgress[] = $task;
            } else {
                $pending[] = $task;
            }
        }

        $doneNo = count($done);
        $inProgressNo = count($inProgress);
        $pendingNo = count($pending);
        $percent = ceil($doneNo / count($tasks) * 100);


        $this->CLImate->br()->output("    <yellow>{$percent}%</yellow> <dark_gray>of all tasks complete.</dark_gray>");
        $this->CLImate->output("    <green>{$doneNo}</green> <dark_gray>done · </dark_gray><blue>{$inProgressNo}</blue> <dark_gray>in-progress · </dark_gray><magenta>{$pendingNo}</magenta> <dark_gray>pending · </dark_gray><cyan>2</cyan> <dark_gray>notes</dark_gray>");
    }
}