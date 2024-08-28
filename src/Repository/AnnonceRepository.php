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

    public function countAnnonces(): int
    {
        return $this->createQueryBuilder('a')
            ->select('count(a.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return Annonce[] Renvoie un tableau d'objets Annonce en fonction des critères de recherche
     */
    public function findBySearchCriteria($data)
    {
        $a = $this->createQueryBuilder('a');

        if (!empty($data->getLieuDepart())) {
            $a->andWhere('a.lieu_depart = :lieu_depart')
               ->setParameter('lieu_depart', $data->getLieuDepart());
        }

        if (!empty($data->getLieuArrivee())) {
            $a->andWhere('a.lieu_arrivee = :lieu_arrivee')
               ->setParameter('lieu_arrivee', $data->getLieuArrivee());
        }

        if (!empty($data->getDate())) {
            $a->andWhere('a.date = :date')
               ->setParameter('date', $data->getDate()->format('Y-m-d'));
        }
        
        if (!empty($data->getService())) {
            $a->andWhere('a.service = :service')
               ->setParameter('service', $data->getService());
        }

        return $a->getQuery()->getResult();
    }

    /**
     * @return Annonce[] Renvoie un tableau d'objets Annonce triés par createdAt en ordre décroissant
     */
    public function findAllOrderedByCreatedAt()
    {
        return $this->createQueryBuilder('a')
            ->orderBy('a.createdAt', 'DESC')
            ->getQuery()->getResult();
    }
}
