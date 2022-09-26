<?php

namespace App\Controller\Visitor\AboutMe;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AboutMeController extends AbstractController
{
    #[Route('/a-propos-de-moi', name: 'visitor.about_me.index')]
    public function index(): Response
    {
        return $this->render('page/visitor/about_me/index.html.twig');
    }
}
