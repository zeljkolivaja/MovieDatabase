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

    protected function addUserMovie(User $user = null, Movie $movie = null, UserMovie $userMovie = null, array $reviewData = null, bool $favorite = null, bool $watchLater = null): void
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


        if ($reviewData != null) {
            $userMovie->setReviewTitle($reviewData['reviewTitle']);
            $userMovie->setReview($reviewData['review']);
        }

        $this->entityManager->persist($userMovie);
        $this->entityManager->flush();
    }
}
