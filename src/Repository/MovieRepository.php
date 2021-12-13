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

    public function createAllReleasedQB(): QueryBuilder
    {
        $qb = $this->createQueryBuilder('m');

        return $this->isReleased($qb)
            ->orderBy('m.releaseYear', 'DESC')
            ->leftJoin('m.categories', 'category')
            ->addSelect('category');
    }

    private function isReleased(QueryBuilder $qb): QueryBuilder
    {
        return $qb->andWhere("m.releaseYear <> ''");
    }

    public function findOneJoinCategory($slug)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.slug = :val')
            ->setParameter('val', $slug)
            //example of many to many join without extra data
            ->leftJoin('m.categories', 'category')
            ->addSelect('category')
            //example of many to many join with additional data in join table, first we left join inner table (personnel)
            //then we inner join that table with person table, then we pass both personnel and person to addSelect method
            //this prevents additional queries
            ->leftJoin('m.personnels', 'personnel')
            ->innerJoin('personnel.person', 'person')
            ->addSelect(['personnel', 'person',])
            ->getQuery()
            ->getOneOrNullResult();
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
