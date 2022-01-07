<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Movie;
use App\Entity\UserMovie;
use App\Repository\MovieRepository;
use App\Repository\UserMovieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


/**
 * @isGranted("IS_AUTHENTICATED_REMEMBERED")
 */
class UserMovieRatingController extends UserMovieController
{

    public function __construct(protected EntityManagerInterface $entityManager, protected MovieRepository $movieRepository, protected UserMovieRepository $userMovieRepository)
    {
        parent::__construct($this->entityManager, $this->movieRepository, $this->userMovieRepository);
    }


    /**
     * @Route("/usermovies/{slug}/{rating<1|2|3|4|5>}", name="app_movie_rate", methods="POST")
     */
    public function rateMovie(string $slug, int $rating, UserMovieRepository $userMovieRepository, MovieRepository $movieRepository): Response
    {

        $user = $this->getUser();
        $movie = $movieRepository->findOneBy(['slug' => $slug]);
        //try to find the relation between user and movie
        $userMovie = $userMovieRepository->findOneBy(["user" => $user, "movie" => $movie]);
        //if there is no relation between user and movie create one
        if ($userMovie == null) {
            $this->addUserMovie($user, $movie);
            $userMovie = $userMovieRepository->findOneBy(["user" => $user, "movie" => $movie]);
        }

        //check if the user already rated movie, if not proceed to rate the movie
        //(because userMovie relation can also be created without rating, by adding the movie to favorites or watchLater list)
        if (!$userMovie->getRated()) {
            $newRating = $movie->getRating() + $rating;
            $newTotalVotes = $movie->getTotalVotes() + 1;

            //calculate score to show the users
            $movieRating = self::calculateRating($newRating, $newTotalVotes);

            //add rating to userMovie object
            $this->userMovieRating($userMovie, $rating);

            //add new TotalVotes and new Rating to movie entity
            $movie->setTotalVotes($newTotalVotes);
            $movie->setRating($newRating);
            $this->entityManager->persist($movie);
            $this->entityManager->flush();
        } else {
            $movieRating = $this->calculateRating($movie->getRating(), $movie->getTotalVotes());
        }

        //return movie rating for ajax
        return $this->json(['movieRating' => $movieRating]);
    }



    private function userMovieRating(UserMovie $userMovie, int $rating): void
    {
        $userMovie->setRating($rating);
        $userMovie->setRated(true);

        $this->entityManager->persist($userMovie);
        $this->entityManager->flush();
    }


    public static function calculateRating(float $rating,  int $totalVotes): string
    {
        return number_format($rating / $totalVotes, 2);
    }
}
