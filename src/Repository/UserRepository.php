<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function countUsers()
    {
        $qb = $this->createQueryBuilder('u');
        return $qb
            ->select('count(u.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function search($q): ?array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.email LIKE :searchTerm OR u.firstName LIKE :searchTerm OR u.lastName LIKE :searchTerm')
            ->setParameter('searchTerm', '%' . $q . '%')
            ->getQuery()
            ->getResult();
    }
}
