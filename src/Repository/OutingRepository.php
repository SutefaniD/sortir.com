<?php

namespace App\Repository;

use App\DTO\SearchFormDTO;
use App\Entity\Outing;
use App\Entity\Participant;
use App\Enum\StatusName;
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
     * @param SearchFormDTO $filter
     * @return array[]
     */
    public function findByFilter(SearchFormDTO $filter, Participant $user) : array
    {

        $queryBuilder = $this->createQueryBuilder('o')
            ->join('o.site', 's')
            ->join('o.status', 'st')
            ->addOrderBy('o.startingDateTime', 'DESC');

        if ($filter->getSite()) {
            $queryBuilder
                ->andWhere('o.site = :site')
                ->setParameter('site', $filter->getSite());
        }

        if ($filter->getOutingName()) {
            $queryBuilder
                ->andWhere('o.name LIKE :name')
                ->setParameter('name', '%' . $filter->getOutingName() . '%');
        }

        if ($filter->getStartDate()) {
            $queryBuilder
                ->andWhere('o.startingDateTime >= :startDate')
                ->setParameter('startDate', $filter->getStartDate());
        }

        if ($filter->getEndDate()) {
            $queryBuilder
                ->andWhere('o.startingDateTime <= :endDate') // TODO : demander client
                ->setParameter('endDate', $filter->getEndDate());
        }

        $orX = $queryBuilder->expr()->orX();

        if ($filter->getIsOrganizer()) {
            $orX->add('o.organizer = :user');
        }

        if ($filter->getIsParticipant()) {
            $orX->add(':user MEMBER OF o.participants');
        }

        if ($filter->getIsNotParticipant()) {
            $orX->add(':user NOT MEMBER OF o.participants');
        }

        if ($orX->count() > 0) {
            $queryBuilder->andWhere($orX)
                ->setParameter('user', $user);
        }

        if ($filter->getIsPast()) {
            $queryBuilder
                ->andWhere('st.label = :pastLabel')
                ->setParameter('pastLabel', StatusName::PAST);
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
