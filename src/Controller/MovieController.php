<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Entity\User;
use App\Entity\UserMovie;
use App\Repository\MovieRepository;
use App\Repository\VideoRepository;
use App\Repository\UserMovieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


class MovieController extends AbstractController
{

    public function __construct(private MovieRepository $movieRepository, private EntityManagerInterface $entityManager)
    {
    }

    /**
     * @Route("/{page<\d+>}", name="app_homepage")
     */
    public function homepage(int $page = 1)
    {

        //find all released  movies, limit by 3,  sort by release date descending 
        $movieQueryBuilder = $this->movieRepository->createAllReleasedQB();

        $pagerfanta = new Pagerfanta(
            new QueryAdapter($movieQueryBuilder)
        );

        $pagerfanta->setMaxPerPage(3);
        $pagerfanta->setCurrentPage($page);

        return $this->render('movie/homepage.html.twig', [
            "pagination" => $pagerfanta
        ]);
    }

    /**
     * @Route("/movies/explore", name="app_movie_explore")
     */
    public function exploreMovies(VideoRepository $videos)
    {
        //TODO most voted movies/ best rated movies, best rated movies by genre, newest movies
        $movies = $this->movieRepository->findMostVotedMovies(10);
        $trailers =  $videos->findLatestVideos(4);

        return $this->render('movie/explore.html.twig', [
            'movies' => $movies,
            'trailers' => $trailers
        ]);
    }


    /**
     * @Route("/movies/search", name="app_movie_search")
     */
    public function search(Request $request)
    {

        //if user entered nothing in search field prevent the search
        if ($request->query->get('q') == "") {
            return $this->redirect($request->headers->get('referer'));
        }

        $movies = $this->movieRepository->search(
            $request->query->get('q')
        );

        return $this->render('movie/search.html.twig', [
            "movies" => $movies,
        ]);
    }


    /**
     * @isGranted("ROLE_ADMIN")
     * @Route("/movies/new", name="app_movie_new")
     */
    public function new()
    {
        dd("hello");
    }


    /**
     * @Route("/movies/{slug}", name="app_movie_show")
     */
    public function show($slug, UserMovieRepository $userMovieRepository)
    {


        $movie = $this->movieRepository->findOneJoinCategory($slug);
        $movie = $this->movieRepository->findOneBy(['slug' => $slug]);

        $userMovie = $userMovieRepository->findOneBy(["user" => $this->getUser(), "movie" => $movie]);

        if (!$movie) {
            throw $this->createNotFoundException("Movie not found");
        }

        if ($movie->getRating() != 0) {
            $movieRating = $this->calculateRating($movie->getRating(), $movie->getTotalVotes());
        } else {
            $movieRating = 0;
        }


        return $this->render('movie/show.html.twig', [
            "movie" => $movie,
            "movieRating" => $movieRating,
            "userData" => $userMovie
        ]);
    }


    /**
     * @Route("/movies/{slug}/{rating<1|2|3|4|5>}", name="app_movie_rate", methods="POST")
     * @isGranted("IS_AUTHENTICATED_REMEMBERED")
     */
    public function rateMovie($slug, int $rating, UserMovieRepository $userMovieRepository)
    {

        $user = $this->getUser();
        $movie = $this->movieRepository->findOneBy(['slug' => $slug]);

        //try to find the relation between user and movie
        $userMovie = $userMovieRepository->findOneBy(["user" => $user, "movie" => $movie]);

        //if there is no join table between user and movie create one and set rated to true
        if ($userMovie == null) {
            $userMovieController = new UserMovieController($this->entityManager);
            $userMovieController->addUserMovie($user, $movie, true);
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
}
