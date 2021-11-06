<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MovieController extends AbstractController
{

    /**
     * @Route("/", name="app_homepage")
     */
    public function homepage(MovieRepository $repository)
    {



        return $this->render('movie/homepage.html.twig', [
            "movies" => $movies
        ]);
    }
}
