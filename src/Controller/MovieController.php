<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Repository\MovieRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class MovieController extends AbstractController
{

    /**
     * @Route("/", name="app_homepage")
     */
    public function homepage(MovieRepository $repository)
    {

        //find all released  movies, limit by 3,  sort by release date descending 
        $movies = $repository->findAllReleasedMoviesDESC(3);

        return $this->render('movie/homepage.html.twig', [
            "movies" => $movies
        ]);
    }


    /**
     * @Route("/movies/new", name="app_movie_new")
     */
    public function new(EntityManagerInterface $entityManager)
    {

        $movie = new Movie();
        $movie->setTitle("Test Movie");
        $movie->setSlug("test-movie-" . rand(0, 1000));
        $movie->setReleaseYear(new DateTime("now"));
        $entityManager->persist($movie);
        $entityManager->flush();
        return new Response("movie added");
    }


    /**
     * @Route("/movies/{slug}", name="app_movie_show")
     */
    public function show(Movie $movie, MovieRepository $repository)
    {
        // $movie = $repository->findOneBy(['slug' => $slug]);


        if (!$movie) {
            throw $this->createNotFoundException("Movie not found");
        }

        return $this->render('movie/show.html.twig', [
            "movie" => $movie
        ]);
    }

    /**
     * @Route("/movies/{movieId}/{rating<1|2|3|4|5>}", name="app_rateMovie", methods="POST")
     */
    public function rateMovie($movieId, int $rating, MovieRepository $repository, EntityManagerInterface $entityManager)
    {

        //in the future prevent the single user from voting the same movie multiple times

        //find movie with id coming from ajax
        $movie = $repository->findOneBy(['id' => $movieId]);

        //get the current score of movie and calculate new score
        $currentScore = $movie->getRating();
        $totalVotes = $movie->getTotalVotes();
        (float)$newScore = ($currentScore + $rating) / ($totalVotes + 1);
        $movieRating = number_format($newScore, 2);

        //save the new score to database
        $movie->setTotalVotes($totalVotes + 1);
        $movie->setRating($currentScore + $rating);
        $entityManager->persist($movie);
        $entityManager->flush();


        //return movie rating for ajax
        return $this->json(['movieRating' => $movieRating]);
    }
}
