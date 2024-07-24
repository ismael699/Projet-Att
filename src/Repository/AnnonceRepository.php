<?php

namespace App\Repository;

use App\Entity\Annonce;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Annonce>
 */
class AnnonceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Annonce::class);
    }

    /**
     * @return Annonce[] Returns an array of Annonce objects based on search criteria
     */
    public function findBySearchCriteria($data)
    {
        $qb = $this->createQueryBuilder('a');

        if (!empty($data->getLieuDepart())) {
            $qb->andWhere('a.lieu_depart = :lieu_depart')
               ->setParameter('lieu_depart', $data->getLieuDepart());
        }

        if (!empty($data->getLieuArrivee())) {
            $qb->andWhere('a.lieu_arrivee = :lieu_arrivee')
               ->setParameter('lieu_arrivee', $data->getLieuArrivee());
        }

        if (!empty($data->getDate())) {
            $qb->andWhere('a.date = :date')
               ->setParameter('date', $data->getDate()->format('Y-m-d'));
        }
        
        if (!empty($data->getService())) {
            $qb->andWhere('a.service = :service')
               ->setParameter('service', $data->getService());
        }

        return $qb->getQuery()->getResult();
    }
}
