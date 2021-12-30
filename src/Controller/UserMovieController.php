<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Entity\User;
use App\Entity\UserMovie;
use App\Repository\MovieRepository;
use App\Repository\UserMovieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @isGranted("IS_AUTHENTICATED_REMEMBERED")
 */
class UserMovieController extends AbstractController
{


    public function __construct(protected EntityManagerInterface $entityManager, protected MovieRepository $movieRepository, protected UserMovieRepository $userMovieRepository)
    {
    }

    protected function addUserMovie(User $user = null, Movie $movie = null, UserMovie $userMovie = null, array $data = null, bool $favorite = null, bool $watchLater = null): void
    {

        if ($userMovie === null) {
            $userMovie = new UserMovie;
            $userMovie->setUser($user);
            $userMovie->setMovie($movie);
        }

        if ($favorite === true) {
            $userMovie->setFavorite(true);
        } elseif ($favorite === false) {
            $userMovie->setFavorite(false);
        }

        if ($watchLater === true) {
            $userMovie->setWatchLater(true);
        } elseif ($watchLater === false) {
            $userMovie->setWatchLater(false);
        }


        if ($data != null) {
            $userMovie->setReviewTitle($data['reviewTitle']);
            $userMovie->setReview($data['review']);
        }

        $this->entityManager->persist($userMovie);
        $this->entityManager->flush();
    }
}
