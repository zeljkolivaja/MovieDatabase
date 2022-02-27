<?php

declare(strict_types=1);


namespace App\Controller;

use App\Repository\MovieRepository;
use App\Repository\VideoRepository;
use App\Repository\UserMovieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Response;

class MovieController extends AbstractController
{

    public function __construct(private MovieRepository $movieRepository, private EntityManagerInterface $entityManager)
    {
    }

    /**
     * @Route("/{page<\d+>}", name="app_homepage")
     */
    public function homepage(int $page = 1): Response
    {
        //find all released  movies, limit by 3,  sort by release date descending
        $movieQueryBuilder = $this->movieRepository->createAllReleasedQB();

        $pagerfanta = new Pagerfanta(
            new QueryAdapter($movieQueryBuilder)
        );
        $pagerfanta->setMaxPerPage(4);
        $pagerfanta->setCurrentPage($page);

        return $this->render('movie/homepage.html.twig', [
            "pagination" => $pagerfanta
        ]);
    }

    /**
     * @Route("/movies/explore", name="app_movie_explore")
     */
    public function exploreMovies(VideoRepository $videos): Response
    {
        //TODO most voted movies/ best rated movies, best rated movies by genre, newest movies
        $mostVotedMovies = $this->movieRepository->findMostVotedMovies(10);
        $bestRatedMovies = $this->movieRepository->findBestRatedMovies(10);
        $trailers =  $videos->findLatestVideos(4);

        return $this->render('movie/explore.html.twig', [
            'mostVotedMovies' => $mostVotedMovies,
            'bestRatedMovies' => $bestRatedMovies,
            'trailers' => $trailers
        ]);
    }


    /**
     * @Route("/movies/search", name="app_movie_search")
     */
    public function search(Request $request): Response
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
     * @Route("/movies/{slug}", name="app_movie_show")
     */
    public function show(string $slug, Request $request, UserMovieRepository $userMovieRepository): Response
    {

        //find movie to display on show page, join with category and personnel
        $movie = $this->movieRepository->findOneJoinCategoryPersonnel($slug);

        if (!$movie) {
            throw $this->createNotFoundException("Movie not found");
        }

        //try to find relation between user and movie (to check did the user vote for movie, did he set movie to favorite, watch later etc)
        $userMovie = $userMovieRepository->findOneBy(["user" => $this->getUser(), "movie" => $movie]);

        //find all reviews for this movie from all users, and make pagination with pagerfanta
        $reviewQueryBuilder = $userMovieRepository->createAllReviewsQB($movie);
        $pagerfanta = new Pagerfanta(
            new QueryAdapter($reviewQueryBuilder)
        );
        $pagerfanta->setMaxPerPage(3);
        $pagerfanta->setCurrentPage((int)$request->query->get("page", 1));

        //if no reviews found set pagerfanta object to null
        if ($pagerfanta->count() == null) {
            $pagerfanta = null;
        }

        if ($movie->getRating() != 0) {
            $movieRating = UserMovieRatingController::calculateRating($movie->getRating(), $movie->getTotalVotes());
        } else {
            $movieRating = 0;
        }

        return $this->render('movie/show.html.twig', [
            "movie" => $movie,
            "movieRating" => $movieRating,
            "userData" => $userMovie,
            "pagination" => $pagerfanta
        ]);
    }
}
