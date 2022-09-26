<?php

namespace App\Controller\Visitor\Welcome;

use App\Repository\TagRepository;
use App\Repository\PostRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class WelcomeController extends AbstractController
{
    #[Route('/', name: 'visitor.welcome.index')]
    public function index(
        CategoryRepository $categoryRepository,
        TagRepository $tagRepository, 
        PostRepository $postRepository
    ): Response
    {
        $categories = $categoryRepository->findAll();
        $tags = $tagRepository->findAll();
        $posts = $postRepository->findBy(array('isPublished' => true));
        return $this->render('page/visitor/welcome/index.html.twig', compact('categories', 'tags', 'posts'));
    }
}
