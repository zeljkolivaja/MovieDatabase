<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     * @Route("/usermovies/favorite/{slug}", name="app_usermovie_favorite")
     */
    public function setFavorite($slug): Response
    {
        $movie = $this->movieRepository->findOneBy(["slug" => $slug]);
        $userMovie = $this->userMovieRepository->findOneBy(["user" => $this->getUser(), "movie" => $movie]);

        if ($userMovie === null) {
            $this->addUserMovie(user: $this->getUser(), movie: $movie, favorite: true);
        } elseif ($userMovie->getFavorite() === false) {
            $this->addUserMovie(userMovie: $userMovie, favorite: true);
        } elseif ($userMovie->getFavorite() === true) {
            $this->addUserMovie(userMovie: $userMovie, favorite: false);
            $this->addFlash('success', 'Movie was removed from your Favorites');
            return $this->redirectToRoute('app_movie_show', ["slug" => $movie->getSlug()]);
        }

        $this->addFlash('success', 'Movie was added to your Favorites');
        return $this->redirectToRoute('app_movie_show', ["slug" => $movie->getSlug()]);
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
}
