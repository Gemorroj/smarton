<?php

namespace App\Command;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CleanerCommand extends Command
{
    protected static $defaultName = 'app:cleaner';

    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Удаление заказаов за до указанной даты')
            ->addArgument('date', InputArgument::REQUIRED, 'Дата до которой должны быть удалены записи')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $date = new \DateTime($input->getArgument('date'));
        $repository = $this->em->getRepository(Order::class);

        $deletedRows = $repository->dropAllUntilThanDate($date);

        $io->success(\sprintf('Операция успешно выполнена. Удалено %d записей старше чем %s.', $deletedRows, $date->format(\DATE_W3C)));
    }
}
