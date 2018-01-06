<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DumperCommand extends Command
{
    protected static $defaultName = 'app:dumper';

    protected function configure()
    {
        $this
            ->setDescription('Дамп записей до указанной даты')
            ->addArgument('date', InputArgument::REQUIRED, 'Дата до которой должны быть сдамплены записи')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $date = new \DateTime($input->getArgument('date'));


        $io->success('Not implemented.');
    }
}
