<?php

namespace App\Repository;

use App\Entity\AllowedDomains;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Scan>
 *
 * @method AllowedDomains|null find($id, $lockMode = null, $lockVersion = null)
 * @method AllowedDomains|null findOneBy(array $criteria, array $orderBy = null)
 * @method AllowedDomains[]    findAll()
 * @method AllowedDomains[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AllowedDomainsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AllowedDomains::class);
    }
}
