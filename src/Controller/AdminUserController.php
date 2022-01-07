<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


/**
 * @isGranted("ROLE_ADMIN")
 */
class AdminUserController extends AbstractController
{

    public function __construct(private UserRepository $userRepository)
    {
    }


    /**
     * @Route("/admin_user/search", name="app_admin_user_search")
     */
    public function search(Request $request): Response
    {

        //if user entered nothing in search field prevent the search
        if ($request->query->get('q') == "") {
            return $this->redirect($request->headers->get('referer'));
        }

        $users = $this->userRepository->search(
            $request->query->get('q')
        );

        return $this->render('admin_user/search.html.twig', [
            "users" => $users,
        ]);
    }
}
