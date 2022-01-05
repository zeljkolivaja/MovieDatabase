<?php

namespace App\Controller;

use App\Form\ReviewFormType;
use App\Repository\MovieRepository;
use App\Repository\UserMovieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;



/**
 * @isGranted("IS_AUTHENTICATED_REMEMBERED")
 */
class UserMovieReviewController extends UserMovieController
{

    public function __construct(protected EntityManagerInterface $entityManager, protected MovieRepository $movieRepository, protected UserMovieRepository $userMovieRepository)
    {
        parent::__construct($this->entityManager, $this->movieRepository, $this->userMovieRepository);
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
            $this->addFlash('success', 'You have already reviewed this Movie');
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
}
