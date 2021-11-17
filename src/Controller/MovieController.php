<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Repository\MovieRepository;
use App\Repository\VideoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

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
     * @Route("/movies/explore", name="app_movie_explore")
     */
    public function exploreMovies(MovieRepository $repository, VideoRepository $videos)
    {
        //TODO most voted movies/ best rated movies, best rated movies by genre, newest movies
        $movies = $repository->findMostVotedMovies(10);
        $trailers =  $videos->findLatestVideos(4);

        return $this->render('movie/explore.html.twig', [
            'movies' => $movies,
            'trailers' => $trailers
        ]);
    }


    /**
     * @Route("/movies/search", name="app_movie_search")
     */
    public function search(Request $request, MovieRepository $repository)
    {

        $movies = $repository->search(
            $request->query->get('q')
        );

        return $this->render('movie/search.html.twig', [
            "movies" => $movies,
        ]);
    }



    /**
     * @Route("/movies/{slug}", name="app_movie_show")
     */
    public function show(Movie $movie)
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


        return $this->render('movie/show.html.twig', [
            "movie" => $movie,
            "movieRating" => $movieRating,
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
