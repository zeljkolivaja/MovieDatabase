<?php

namespace App\Repository;

use App\Entity\UserMovie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserMovie|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserMovie|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserMovie[]    findAll()
 * @method UserMovie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserMovieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserMovie::class);
    }

    public function createAllReviewsQB($movie): QueryBuilder
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.movie = :val')
            ->setParameter('val', $movie)
            ->andWhere('u.review is NOT NULL')
            ->orderBy('u.id', 'ASC');
    }
}
