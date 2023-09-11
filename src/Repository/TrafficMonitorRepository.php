<?php

namespace App\Repository;

use App\Entity\TrafficMonitor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TrafficMonitor>
 *
 * @method TrafficMonitor|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrafficMonitor|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrafficMonitor[]    findAll()
 * @method TrafficMonitor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrafficMonitorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrafficMonitor::class);
    }

//    /**
//     * @return TrafficMonitor[] Returns an array of TrafficMonitor objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?TrafficMonitor
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
