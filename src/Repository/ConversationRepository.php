<?php

namespace App\Repository;

use App\Entity\Conversation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Conversation>
 */
class ConversationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Conversation::class);
    }

    // public function findConversationsByUserSortedByLastMessage($user)
    // {
        // return $this->createQueryBuilder('c')
            // ->leftJoin('c.messages', 'm')
            // ->leftJoin('c.annonce', 'a')
            // ->where('c.creator = :user OR a.user = :user')
            // ->setParameter('user', $user)
            // ->orderBy('m.createdAt', 'DESC')
            // ->groupBy('c.id')
            // ->getQuery()
            // ->getResult();
    // }
}
