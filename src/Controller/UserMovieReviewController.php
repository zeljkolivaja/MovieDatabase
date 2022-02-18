<?php

declare(strict_types=1);



namespace App\Controller;

use App\Entity\Movie;
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
    public function review(Movie $movie, Request $request): Response
    {
        $userMovie = $this->getUserMovie($movie);

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
                $this->addReview($movie, $data);
            } else {
                $this->addReview(data: $data, userMovie: $userMovie,);
            }
            $this->addFlash('success', 'Your Review has been published');
            return $this->redirectToRoute('app_movie_show', ["slug" => $movie->getSlug()]);
        }

        return $this->render('usermovies/review.html.twig', [
            'reviewForm' => $form->createView(),
            'movie' => $movie
        ]);
    }


    private function addReview($data, $movie = null, $userMovie = null)
    {
        if ($userMovie === null) {
            $userMovie = $this->getUserMovie($movie)
                ->setReviewTitle($data['reviewTitle'])
                ->setReview($data['review']);
        } else {
            $userMovie->setReviewTitle($data['reviewTitle'])
                ->setReview($data['review']);
        }

        $this->saveUserMovie($userMovie);
    }


    /**
     * @Route("/usermovies/editReview/{slug}", name="app_usermovies_editReview")
     */
    public function editReview(Movie $movie, Request $request): Response
    {
        $userMovie = $this->getUserMovie($movie);

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
            $this->addReview(data: $data, userMovie: $userMovie,);
            $this->addFlash('success', 'Your Review has been updated');
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
    public function deleteReview(Movie $movie): Response
    {
        $userMovie = $this->getUserMovie($movie)->setReview(NULL);
        $this->saveUserMovie($userMovie);

        $this->addFlash('danger', 'Your Review was deleted');
        return $this->redirectToRoute('app_movie_show', ["slug" => $movie->getSlug()]);
    }
}
