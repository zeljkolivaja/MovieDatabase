<?php

namespace App\Repository;

use App\Entity\Movie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Movie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Movie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Movie[]    findAll()
 * @method Movie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MovieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Movie::class);
    }

    // /**
    //  * @return Movie[] Returns an array of Movie objects
    //  */

    public function findAllReleasedMoviesDESC($numberOfresults): ?array
    {
        $qb = $this->createQueryBuilder('m');

        return $this->isReleased($qb)
            ->orderBy('m.releaseYear', 'DESC')
            ->setMaxResults($numberOfresults)
            ->getQuery()
            ->getResult();
    }

    private function isReleased(QueryBuilder $qb): QueryBuilder
    {
        return $qb->andWhere('m.releaseYear IS NOT NULL');
    }

    public function findMostVotedMovies($numberOfresults)
    {
        $qb = $this->createQueryBuilder('m');

        return $this->isReleased($qb)
            ->orderBy('m.totalVotes', 'DESC')
            ->setMaxResults($numberOfresults)
            ->getQuery()
            ->getResult();
    }

    public function search($q): ?array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.title LIKE :searchTerm')
            ->setParameter('searchTerm', '%' . $q . '%')
            ->getQuery()
            ->getResult();
    }
}
