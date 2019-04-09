<?php
/**
 * Created by human.
 * User: Weinan Tang <twn39@163.com>
 * Date: 2019-04-09
 * Time: 15:49
 */
namespace App\Commands;

use League\CLImate\CLImate;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Note extends Command
{
    protected static $defaultName = 'note';
    /**
     * @var CLImate
     */
    private $CLImate;

    public function __construct(CLImate $CLImate)
    {
        parent::__construct();
        $this->CLImate = $CLImate;
    }

    public function configure()
    {
        $this->setDescription("Note command")
             ->setDefinition(
                 new InputDefinition([
                     new InputOption('title', 't', InputOption::VALUE_OPTIONAL, 'create a task'),
                     new InputOption('board', 'b', InputOption::VALUE_OPTIONAL, 'special a board', 'Board'),
                     new InputOption('check', 'c', InputOption::VALUE_OPTIONAL, 'check a task'),
                     new InputOption('start', 's', InputOption::VALUE_OPTIONAL, 'start a task'),
                     new InputOption('archive', 'a', InputOption::VALUE_OPTIONAL, 'archive a task'),
                 ])
             )
             ->setHelp('Note command');
    }


    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('notes');
    }
}