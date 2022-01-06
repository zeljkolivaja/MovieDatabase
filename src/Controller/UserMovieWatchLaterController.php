<?php

namespace App\Controller;

use App\Entity\Movie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\MovieRepository;
use App\Repository\UserMovieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


/**
 * @isGranted("IS_AUTHENTICATED_REMEMBERED")
 */
class UserMovieWatchLaterController extends UserMovieController
{
    public function __construct(protected EntityManagerInterface $entityManager, protected MovieRepository $movieRepository, protected UserMovieRepository $userMovieRepository)
    {
        parent::__construct($this->entityManager, $this->movieRepository, $this->userMovieRepository);
    }


    /**
     * @Route("/usermovies/watchlater/{slug}", name="app_usermovie_setwatchlater")
     */
    public function setWatchLater(Movie $movie): Response
    {
        $userMovie = $this->userMovieRepository->findOneBy(["user" => $this->getUser(), "movie" => $movie]);

        if ($userMovie === null) {
            $this->addUserMovie(user: $this->getUser(), movie: $movie, watchLater: true);
        } elseif ($userMovie->getWatchLater() === false) {
            $this->addUserMovie(userMovie: $userMovie, watchLater: true);
        } elseif ($userMovie->getWatchLater() === true) {
            $this->addUserMovie(userMovie: $userMovie, watchLater: false);
            $this->addFlash('danger', 'Movie was removed from your Watch List');
            return $this->redirectToRoute('app_movie_show', ["slug" => $movie->getSlug()]);
        }

        $this->addFlash('success', 'Movie was added to your Watch List');
        return $this->redirectToRoute('app_movie_show', ["slug" => $movie->getSlug()]);
    }

    /**
     * @Route("/usermovies/watchlater/", name="app_usermovie_watchlater")
     */
    public function watchLater(): Response
    {
        $watchLaterList = $this->userMovieRepository->findBy(["user" => $this->getUser(), "watchLater" => true], ["rating" => 'DESC']);
        // dd($watchLaterList);

        return $this->render('/usermovies/watchlater.html.twig', [
            'watchLaterList' => $watchLaterList
        ]);
    }
}
