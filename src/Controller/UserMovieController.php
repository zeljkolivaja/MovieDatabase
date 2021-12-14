<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Entity\User;
use App\Entity\UserMovie;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserMovieController extends AbstractController
{


    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function addUserMovie(User $user, Movie $movie, bool $rated = null, bool $favorite = null)
    {

        $userMovie = new UserMovie;
        $userMovie->setUser($user);
        $userMovie->setMovie($movie);

        if ($rated != null) {
            $userMovie->setRated(true);
        }

        if ($favorite != null) {
            $userMovie->setFavorite($favorite);
        }

        $this->entityManager->persist($userMovie);
        $this->entityManager->flush();
    }
}
