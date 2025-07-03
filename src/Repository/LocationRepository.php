<?php

namespace App\Repository;

use App\Entity\Location;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Location>
 */
class LocationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Location::class);
    }

    /**
    //     * @return Location[] Returns an array of Location objects
    //     */
    public function findAllWithFilters(?int $userId, ?string $siteFilter): array
    {
        $qb = $this->createQueryBuilder('o')
            ->leftJoin('o.site', 's')
            ->addSelect('s');

        if ($siteFilter) {
            $qb->andWhere('s.id = :siteId')
                ->setParameter('siteId', $siteFilter);
        }

        if ($userId) {
            $qb->leftJoin('o.participants', 'p')
                ->addSelect('p');
            // Tu peux ajouter d'autres conditions si nécessaire, comme :
            // $qb->andWhere('p.id = :userId')
            //    ->setParameter('userId', $userId);
        }

        return $qb->getQuery()->getResult();
    }

    //    /**
    //     * @return Location[] Returns an array of Location objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('l.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Location
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
