<?php

namespace App\Controller\User\Home;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/utilisateur/espace-prive', name: 'user.home.index')]
    public function index(): Response
    {
        return $this->render('page/user/home/index.html.twig');
    }
}
