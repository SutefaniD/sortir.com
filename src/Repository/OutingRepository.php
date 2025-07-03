<?php

namespace App\Repository;

use App\DTO\OutingFilterDTO;
use App\Entity\Outing;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Outing>
 */
class OutingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Outing::class);
    }

    /**
     * @param OutingFilterDTO $filter
     * @return array[]
     */
    public function findByFilter(OutingFilterDTO $filter) : array
    {
        $queryBuilder = $this->createQueryBuilder('o')
            ->join('o.site', 's');

        if ($filter->getSite()) {
            $queryBuilder
                ->andWhere('o.site = :site')
                ->setParameter('site', $filter->getSite());
        }

        if ($filter->getOutingName()) {
            $queryBuilder
                ->andWhere('o.name LIKE :name')  // TODO
                ->setParameter('name', '%' . $filter->getOutingName() . '%');
        }

        if ($filter->getStartDate()) {
            $queryBuilder
                ->andWhere('o.startingDateTime >= :start')
                ->setParameter('start', $filter->getStartDate());
        }

        if ($filter->getEndDate()) {
            $queryBuilder
                ->andWhere('o.startingDateTime <= :end') // TODO : demander client
                ->setParameter('end', $filter->getEndDate());
        }

        return $queryBuilder->getQuery()->getResult();

    }

//    /**
//     * @return Outing[] Returns an array of Outing objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('o.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Outing
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
