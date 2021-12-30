<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminMovieController extends AbstractController
{
    #[Route('/admin/movie', name: 'admin_movie')]
    public function index(): Response
    {
        return $this->render('admin_movie/index.html.twig', [
            'controller_name' => 'AdminMovieController',
        ]);
    }
}
