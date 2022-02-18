<?php

declare(strict_types=1);

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
     * @Route("/usermovies/watchlater/", name="app_usermovie_watchlater")
     */
    public function watchLater(): Response
    {
        $watchLaterList = $this->userMovieRepository->findBy(["user" => $this->getUser(), "watchLater" => true]);

        return $this->render('/usermovies/watchlater.html.twig', [
            'watchLaterList' => $watchLaterList
        ]);
    }

    /**
     * @Route("usermovies/addWatchLater{slug}", name="app_usermovie_addwatchlater")
     */
    public function addWatchLater(Movie $movie): Response
    {
        $userMovie = $this->getUserMovie($movie)->setWatchLater(true);
        $this->saveUserMovie($userMovie);

        $this->addFlash('success', 'Movie was added to your Watch List');
        return $this->redirectToRoute('app_movie_show', ["slug" => $movie->getSlug()]);
    }

    /**
     * @Route("usermovies/removeWatchLater{slug}", name="app_usermovie_removewatchlater")
     */
    public function removeWatchLater(Movie $movie): Response
    {
        $userMovie = $this->getUserMovie($movie)->setWatchLater(false);
        $this->saveUserMovie($userMovie);

        $this->addFlash('danger', 'Movie was removed from your Watch List');
        return $this->redirectToRoute('app_movie_show', ["slug" => $movie->getSlug()]);
    }
}
