<?php

namespace App\Controller\Admin\Home;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('ROLE_ADMIN')]
class HomeController extends AbstractController
{
    #[Route('/administrateur/espace-prive', name: 'admin.home.index')]
    public function index(): Response
    {
        return $this->render('page/admin/home/index.html.twig');
    }
}
