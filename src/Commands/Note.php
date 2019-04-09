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
use Symfony\Component\Console\Input\InputInterface;
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
             ->setHelp('Note command');
    }


    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('notes');
    }
}