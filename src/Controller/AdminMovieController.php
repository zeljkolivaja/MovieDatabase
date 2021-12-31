<?php

namespace App\Controller;

use App\Entity\Movie;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;



/**
 * @isGranted("ROLE_ADMIN")
 */
class AdminMovieController extends AbstractController
{

    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    /**
     *@Route("admin_movie/", name="app_admin_movie_index")
     */
    public function index(): Response
    {
        return $this->render("/admin_movie/index.html.twig");
    }


    /**
     * @Route("/movie/delete/{slug}", name="app_admin_movie_delete")
     */
    public function delete(Movie $movie): Response
    {
        $movieName = $movie->getTitle();
        $this->entityManager->remove($movie);
        $this->entityManager->flush();


        $this->addFlash('success', "Movie '$movieName' successfully deleted");
        return $this->redirectToRoute('app_homepage');
    }
}
