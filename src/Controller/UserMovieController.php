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
            $userMovie->setRated($rated);
        }

        if ($favorite != null) {
            $userMovie->setFavorite($favorite);
        }

        $this->entityManager->persist($userMovie);
        $this->entityManager->flush();
    }


    /**
     * @Route("/usermovies/{slug}/{rating<1|2|3|4|5>}", name="app_movie_rate", methods="POST")
     * @isGranted("IS_AUTHENTICATED_REMEMBERED")
     */
    public function rateMovie($slug, int $rating, UserMovieRepository $userMovieRepository, MovieRepository $movieRepository)
    {

        $user = $this->getUser();
        $movie = $movieRepository->findOneBy(['slug' => $slug]);

        //try to find the relation between user and movie
        $userMovie = $userMovieRepository->findOneBy(["user" => $user, "movie" => $movie]);

        //if there is no join table between user and movie create one and set rated to true
        if ($userMovie == null) {
            $this->addUserMovie($user, $movie, true);
        }

        //calculate new rating
        $newRating = $movie->getRating() + $rating;
        //calculate new total votes
        $newTotalVotes = $movie->getTotalVotes() + 1;
        //calculate score to show the users
        $movieRating = $this->calculateRating($newRating, $newTotalVotes);

        //add new totalvotes and rating to DB
        $movie->setTotalVotes($newTotalVotes);
        $movie->setRating($newRating);
        $this->entityManager->persist($movie);
        $this->entityManager->flush();

        //return movie rating for ajax
        return $this->json(['movieRating' => $movieRating]);
    }


    private function calculateRating($totalVotes, $rating)
    {
        return number_format($totalVotes / $rating, 2);
    }


    public function addReview()
    {
        //try to find does the relation between the User and Movie already exist, if true set the $userMovie to that object
        //if there is no relation between user and move create new userMovie object
    }

    public function editReview()
    {
        # code...
    }

    public function setFavorite()
    {
        # code...
    }

    public function setWatchLater()
    {
        # code...
    }
}
