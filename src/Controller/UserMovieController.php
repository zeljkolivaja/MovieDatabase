<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Entity\User;
use App\Entity\UserMovie;
use App\Form\ReviewFormType;
use App\Repository\MovieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;

class UserMovieController extends AbstractController
{


    public function __construct(private EntityManagerInterface $entityManager, private MovieRepository $movieRepository)
    {
    }


    /**
     * @isGranted("IS_AUTHENTICATED_REMEMBERED")
     * @Route("/usermovies/review/{slug}", name="app_usermovies_review")
     */
    public function review($slug, Request $request)
    {

        $movie = $this->movieRepository->findOneBy(["slug" => $slug]);

        $form = $this->createForm(ReviewFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $movie = $this->movieRepository->findOneBy(["slug" => $request->request->get('movie')]);
            $data = $form->getData();

            $userMovie = new UserMovie;
            $userMovie->setMovie($movie);
            $userMovie->setUser($this->getUser());
            $userMovie->setReviewTitle($data['reviewTitle']);
            $userMovie->setReview($data['review']);

            $this->entityManager->persist($userMovie);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_movie_show', ["slug" => $movie->getSlug()]);
        }

        return $this->render('usermovies/review.html.twig', [
            'reviewForm' => $form->createView(),
            'movie' => $movie
        ]);
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


    private function addUserMovie(User $user, Movie $movie)
    {
        $userMovie = new UserMovie;
        $userMovie->setUser($user);
        $userMovie->setMovie($movie);

        $this->entityManager->persist($userMovie);
        $this->entityManager->flush();
    }

    private function userMovieRating(UserMovie $userMovie, $rating)
    {
        $userMovie->setRating($rating);
        $userMovie->setRated(true);

        $this->entityManager->persist($userMovie);
        $this->entityManager->flush();
    }



    public static function calculateRating($totalVotes, $rating)
    {
        return number_format($totalVotes / $rating, 2);
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
