<?php
namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class OrderRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Order::class);
    }


    /**
     * @param \DateTime $dateTime
     * @return int count deleted rows
     */
    public function dropAllUntilThanDate(\DateTime $dateTime) : int
    {
        $q = $this->createQueryBuilder('o')
            ->delete()
            ->where('o.dateCreate < :dateCreate')
            ->setParameter('dateCreate', $dateTime)
            ->getQuery();

        return $q->execute();
    }
}
