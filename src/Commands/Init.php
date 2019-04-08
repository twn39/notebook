<?php
namespace App\Commands;

use League\CLImate\CLImate;
use Illuminate\Database\Capsule\Manager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Illuminate\Database\Capsule\Manager as Capsule;

class Init extends Command
{

    /**
     * @var CLImate
     */
    private $CLImate;

    private $configDir;

    private $configFile;

    protected static $defaultName = 'init';

    public function __construct(CLImate $CLImate)
    {
        parent::__construct();
        $this->CLImate = $CLImate;
    }


    public function configure()
    {
        $this->setDescription("Init the database")
            ->setHelp('Init the database');
    }


    public function execute(InputInterface $input, OutputInterface $output)
    {
        $userProfileDir = $_SERVER['USERPROFILE'] ?? $_SERVER['HOME'];
        $this->configDir = $userProfileDir . '/.nb';
        $this->configFile = $this->configDir . '/config.json';

        if (!file_exists($this->configDir)) {
            mkdir($this->configDir);
        }

        if (!file_exists($this->configFile)) {

            $config = [
                'DB' => [
                    'driver' => 'sqlite',
                    'database' => $this->configDir.'/db.sqlite',
                    'prefix'    => '',
                ],
            ];

            file_put_contents($this->configFile, json_encode($config, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            // migration
            $this->migration();
        }

    }


    private function migration()
    {
        $dbFile = $this->configDir . '/db.sqlite';

        if (!file_exists($dbFile)) {
            touch($dbFile);
        }

        $capsule = new Capsule;

        $capsule->addConnection([
            'driver'    => 'sqlite',
            'database'  => $dbFile,
            'prefix'    => '',
        ]);

        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        $boardSQL = <<<SQL
create table boards
(
    id         integer not null
        constraint boards_pk
            primary key autoincrement,
    name       text    not null,
    created_at text    not null
);
SQL;
        $taskSQL = <<<SQL
create table tasks
(
    id         integer
        constraint tasks_pk
            primary key autoincrement,
    title      text,
    board_id   integer,
    status     integer,
    created_at text,
    updated_at text,
    priority   int default 0 not null,
    star       int default 0
);

create index tasks_board_id_index
    on tasks (board_id);
SQL;

        $conn = $capsule->getConnection();

        $conn->select($boardSQL);
        $conn->select($taskSQL);

        $conn->table('boards')->insert([
            'name' => 'Board',
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $this->CLImate->br()->output('   <green> 🎉  DB init success</green>');
        $this->CLImate->output('');
    }
}
