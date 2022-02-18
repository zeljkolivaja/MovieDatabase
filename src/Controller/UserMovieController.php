<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Movie;
use App\Entity\User;
use App\Entity\UserMovie;
use App\Repository\MovieRepository;
use App\Repository\UserMovieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


/**
 * @isGranted("IS_AUTHENTICATED_REMEMBERED")
 */
class UserMovieController extends AbstractController
{


    public function __construct(protected EntityManagerInterface $entityManager, protected MovieRepository $movieRepository, protected UserMovieRepository $userMovieRepository)
    {
    }

    protected function getUserMovie($movie, $userMovie = null): UserMovie
    {
        $userMovie = $this->userMovieRepository->findOneBy(["user" => $this->getUser(), "movie" => $movie]);


        if ($userMovie === null) {
            $userMovie = new UserMovie;
            $userMovie->setUser($this->getUser());
            $userMovie->setMovie($movie);
        }

        return $userMovie;
    }


    public function saveUserMovie($userMovie)
    {
        $this->entityManager->persist($userMovie);
        $this->entityManager->flush();
    }
}
