<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Entity\User;
use App\Repository\MovieRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;

/**
 * @isGranted("ROLE_ADMIN")
 */
class AdminMovieController extends AbstractController
{

    public function __construct(private EntityManagerInterface $entityManager, private MovieRepository $movieRepository)
    {
    }

    /**
     *@Route("admin_movie/", name="app_admin_movie_index")
     */
    public function index(Request $request, UserRepository $userRepository): Response
    {

        $lastUser = $userRepository->findOneBy([], ['id' => 'DESC']);
        $userCount = $userRepository->countUsers();

        $movieQueryBuilder = $this->movieRepository->createAllMoviesQB();
        $pagerfanta = new Pagerfanta(
            new QueryAdapter($movieQueryBuilder)
        );
        $pagerfanta->setMaxPerPage(10);
        $pagerfanta->setCurrentPage($request->query->get("page", 1));


        return $this->render('admin_movie/index.html.twig', [
            "pagination" => $pagerfanta,
            "lastUser" => $lastUser,
            "userCount" => $userCount,
        ]);
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
