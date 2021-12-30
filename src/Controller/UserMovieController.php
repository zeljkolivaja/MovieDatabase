<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Entity\User;
use App\Entity\UserMovie;
use App\Form\ReviewFormType;
use App\Repository\MovieRepository;
use App\Repository\UserMovieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;

/**
 * @isGranted("IS_AUTHENTICATED_REMEMBERED")
 */
class UserMovieController extends AbstractController
{


    public function __construct(protected EntityManagerInterface $entityManager, protected MovieRepository $movieRepository, protected UserMovieRepository $userMovieRepository)
    {
    }

    /**
     * @Route("/usermovies/review/{slug}", name="app_usermovies_review")
     */
    public function review($slug, Request $request): Response
    {
        $movie = $this->movieRepository->findOneBy(["slug" => $slug]);
        $userMovie = $this->userMovieRepository->findOneBy(["user" => $this->getUser(), "movie" => $movie]);

        //prevent user from submiting multiple reviews
        if ($userMovie != null && $userMovie->getReview() != null) {
            $this->addFlash('success', 'You have already rated this Movie');
            return $this->redirectToRoute('app_movie_show', ["slug" => $movie->getSlug()]);
        }

        $form = $this->createForm(ReviewFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();

            //find the movie user wishes to review
            $movie = $this->movieRepository->findOneBy(["slug" => $data['movie']]);
            //if the userMovie entity is null, create new one, else update the one we already found
            if ($userMovie == null) {
                $this->addUserMovie(user: $this->getUser(), movie: $movie, data: $data);
            } else {
                $this->addUserMovie(userMovie: $userMovie, data: $data);
            }
            $this->addFlash('success', 'Your Review has been published');
            return $this->redirectToRoute('app_movie_show', ["slug" => $movie->getSlug()]);
        }

        return $this->render('usermovies/review.html.twig', [
            'reviewForm' => $form->createView(),
            'movie' => $movie
        ]);
    }


    /**
     * @Route("/usermovies/editReview/{slug}", name="app_usermovies_editReview")
     */
    public function editReview($slug, Request $request): Response
    {
        $movie = $this->movieRepository->findOneBy(["slug" => $slug]);
        $userMovie = $this->userMovieRepository->findOneBy(["user" => $this->getUser(), "movie" => $movie]);

        //prevent user from editing if he has no review already submitted
        if ($userMovie == null or $userMovie->getReview() == null) {
            return $this->redirectToRoute('app_movie_show', ["slug" => $movie->getSlug()]);
        }

        $editData = ["reviewTitle" => $userMovie->getReviewTitle(), "review" => $userMovie->getReview()];
        $form = $this->createForm(ReviewFormType::class, $editData);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();
            //find the movie user wishes to review
            $movie = $this->movieRepository->findOneBy(["slug" => $data['movie']]);

            //if the userMovie entity is null, create new one, else update the one we already found
            if ($userMovie == null) {
                $this->addUserMovie(user: $this->getUser(), movie: $movie, data: $data);
            } else {
                $this->addUserMovie(userMovie: $userMovie, data: $data);
            }
            $this->addFlash('success', 'Your Review has been published');
            return $this->redirectToRoute('app_movie_show', ["slug" => $movie->getSlug()]);
        }

        return $this->render('usermovies/edit_review.html.twig', [
            'reviewForm' => $form->createView(),
            'movie' => $movie
        ]);
    }


    protected function addUserMovie(User $user = null, Movie $movie = null, UserMovie $userMovie = null, array $data = null, bool $favorite = null, bool $watchLater = null): void
    {

        if ($userMovie === null) {
            $userMovie = new UserMovie;
            $userMovie->setUser($user);
            $userMovie->setMovie($movie);
        }

        if ($favorite === true) {
            $userMovie->setFavorite(true);
        } elseif ($favorite === false) {
            $userMovie->setFavorite(false);
        }

        if ($watchLater === true) {
            $userMovie->setWatchLater(true);
        } elseif ($watchLater === false) {
            $userMovie->setWatchLater(false);
        }


        if ($data != null) {
            $userMovie->setReviewTitle($data['reviewTitle']);
            $userMovie->setReview($data['review']);
        }

        $this->entityManager->persist($userMovie);
        $this->entityManager->flush();
    }



    /**
     * @Route("/usermovies/delete/{slug}", name="app_usermovie_deleteReview")
     */
    public function deleteReview($slug)
    {
        $movie = $this->movieRepository->findOneBy(["slug" => $slug]);
        $userMovie = $this->userMovieRepository->findOneBy(["user" => $this->getUser(), "movie" => $movie]);

        $userMovie->setReview(NULL);
        $this->entityManager->persist($userMovie);
        $this->entityManager->flush();
        $this->addFlash('success', 'Your Review was deleted');
        return $this->redirectToRoute('app_movie_show', ["slug" => $movie->getSlug()]);
    }



    /**
     * @Route("/usermovies/favorite/{slug}", name="app_usermovie_favorite")
     */
    public function setFavorite($slug)
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
     * @Route("/usermovies/watchlater/{slug}", name="app_usermovie_setwatchlater")
     */
    public function setWatchLater($slug)
    {
        $movie = $this->movieRepository->findOneBy(["slug" => $slug]);
        $userMovie = $this->userMovieRepository->findOneBy(["user" => $this->getUser(), "movie" => $movie]);

        if ($userMovie === null) {
            $this->addUserMovie(user: $this->getUser(), movie: $movie, watchLater: true);
        } elseif ($userMovie->getWatchLater() === false) {
            $this->addUserMovie(userMovie: $userMovie, watchLater: true);
        } elseif ($userMovie->getWatchLater() === true) {
            $this->addUserMovie(userMovie: $userMovie, watchLater: false);
            $this->addFlash('success', 'Movie was removed from your Watch List');
            return $this->redirectToRoute('app_movie_show', ["slug" => $movie->getSlug()]);
        }

        $this->addFlash('success', 'Movie was added to your Watch List');
        return $this->redirectToRoute('app_movie_show', ["slug" => $movie->getSlug()]);
    }


    /**
     * @Route("/usermovies/watchlater/", name="app_usermovie_watchlater")
     */
    public function watchLater()
    {
        $watchLaterList = $this->userMovieRepository->findBy(["user" => $this->getUser(), "watchLater" => true], ["rating" => 'DESC']);
        // dd($watchLaterList);

        return $this->render('/usermovies/watchlater.html.twig', [
            'watchLaterList' => $watchLaterList
        ]);
    }


    /**
     * @Route("/usermovies/favorites/", name="app_usermovie_favorites")
     */
    public function favorites()
    {
        $favoritesList = $this->userMovieRepository->findBy(["user" => $this->getUser(), "favorite" => true], ["rating" => 'DESC']);
        return $this->render('/usermovies/favorites.html.twig', [
            'favoritesList' => $favoritesList
        ]);
    }
}
