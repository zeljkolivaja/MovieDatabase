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

        if (!$movie) {
            throw $this->createNotFoundException("Movie not found");
        }

        if ($movie->getRating() != 0) {
            $movieRating = $movie->getRating() / $movie->getTotalVotes();
            $movieRating = number_format($movieRating, 2);
        } else {
            $movieRating = 0;
        }

        $media = $movie->getMedia();


        return $this->render('movie/show.html.twig', [
            "movie" => $movie,
            "movieRating" => $movieRating,
            "media" => $media
        ]);
    }

    /**
     * @Route("/movies/{slug}/{rating<1|2|3|4|5>}", name="app_movie_rate", methods="POST")
     */
    public function rateMovie($slug, int $rating, EntityManagerInterface $entityManager, MovieRepository $repository)
    {

        //in the future prevent the single user from voting the same movie multiple times

        //find movie with slug
        $movie = $repository->findOneBy(['slug' => $slug]);


        //calculate new rating
        $newRating = $movie->getRating() + $rating;

        //calculate new total votes
        $newTotalVotes = $movie->getTotalVotes() + 1;

        //calculate score to show the users
        $newScore = $newRating / $newTotalVotes;
        $movieRating = number_format($newScore, 2);

        //add new totalvotes and rating to DB
        $movie->setTotalVotes($newTotalVotes);
        $movie->setRating($newRating);
        $entityManager->persist($movie);
        $entityManager->flush();


        //return movie rating for ajax
        return $this->json(['movieRating' => $movieRating]);
    }
}
