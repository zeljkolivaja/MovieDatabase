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
class UserMovieFavoriteController extends UserMovieController
{
    public function __construct(protected EntityManagerInterface $entityManager, protected MovieRepository $movieRepository, protected UserMovieRepository $userMovieRepository)
    {
        parent::__construct($this->entityManager, $this->movieRepository, $this->userMovieRepository);
    }


    /**
     * @Route("/usermovies/favorites/", name="app_usermovie_favorites")
     */
    public function favorites(): Response
    {
        $favoritesList = $this->userMovieRepository->findBy(["user" => $this->getUser(), "favorite" => true], ["rating" => 'DESC']);
        return $this->render('/usermovies/favorites.html.twig', [
            'favoritesList' => $favoritesList
        ]);
    }


    /**
     * @Route("/usermovies/addFavorite/{slug}", name="app_usermovie_addfavorite")
     */
    public function addFavorite(Movie $movie): Response
    {
        $userMovie = $this->getUserMovie($movie)->setFavorite(true);
        $this->saveUserMovie($userMovie);

        $this->addFlash('success', 'Movie was added to your Favorites');
        return $this->redirectToRoute('app_movie_show', ["slug" => $movie->getSlug()]);
    }


    /**
     * @Route("/usermovies/removeFavorite/{slug}", name="app_usermovie_removefavorite")
     */
    public function removeFavorite(Movie $movie): Response
    {
        $userMovie = $this->getUserMovie($movie)->setFavorite(false);
        $this->saveUserMovie($userMovie);

        $this->addFlash('danger', 'Movie was removed from your Favorites');
        return $this->redirectToRoute('app_movie_show', ["slug" => $movie->getSlug()]);
    }
}
