<?php

namespace App\Command;

use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;

class DumperCommand extends Command
{
    protected static $defaultName = 'app:dumper';

    /** @var ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Дамп записей до указанной даты')
            ->addArgument('date', InputArgument::REQUIRED, 'Дата до которой должны быть сдамплены записи')
            ->addArgument('path', InputArgument::REQUIRED, 'Путь к файлу в который запишется дамп')
            ->addOption('gzip', null, InputOption::VALUE_NONE, 'Запаковать дамп с помощью gzip')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $date = new \DateTime($input->getArgument('date'));

        /** @var \Doctrine\DBAL\Connection $dbConnection */
        $dbConnection = $this->container->get('doctrine')->getConnection();

        // https://dev.mysql.com/doc/refman/5.7/en/mysqldump.html
        $cmd = [
            'mysqldump',
            \escapeshellarg($dbConnection->getDatabase()),
            'orders',
            '--user=' . \escapeshellarg($dbConnection->getUsername()),
            '--password=' . \escapeshellarg($dbConnection->getPassword()),
            '--host=' . \escapeshellarg($dbConnection->getHost()),
            '--port=' . \escapeshellarg($dbConnection->getPort()),
            '--single-transaction',
            \sprintf('--where="date_create < \"%s\""', $date->format('Y-m-d H:i:s.u')),
        ];
        if ($input->getOption('gzip')) {
            $cmd[] = '| gzip';
        }
        $cmd[] = '>';
        $cmd[] = \escapeshellarg($input->getArgument('path'));

        $process = new Process(\implode(' ', $cmd));
        $process->mustRun();

        // mysqldump "smarton" orders -u "root" --password="" --host="127.0.0.1" --port="3306" --single-transaction --where="date_create < \"2018-01-06 00:00:00.000000\"" > "/dump.sql"
        $io->success('Дамп успешно создан');
    }
}
