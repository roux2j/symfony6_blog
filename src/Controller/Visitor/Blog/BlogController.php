<?php

namespace App\Controller\Visitor\Blog;

use App\Entity\Tag;
use App\Entity\Post;
use App\Entity\Category;
use App\Repository\TagRepository;
use App\Repository\PostRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BlogController extends AbstractController
{
    #[Route('/blog/liste-des-articles', name: 'visitor.blog.index')]
    public function index(
        CategoryRepository $categoryRepository,
        TagRepository $tagRepository, 
        PostRepository $postRepository
        ): Response
    {
        $categories = $categoryRepository->findAll();
        $tags = $tagRepository->findAll();
        $posts = $postRepository->findBy(array('isPublished' => true));

        return $this->render('page/visitor/blog/index.html.twig', compact('categories', 'tags', 'posts'));
    }

    #[Route('/blog/{id<\d+>}/{slug}', name: 'visitor.blog.post.show')]
    public function show(Post $post): Response
    {
        return $this->render("page/visitor/blog/show.html.twig", compact('post'));
    }

    #[Route('/blog/{id<\d+>}/{slug}/filtre-par-categorie', name: 'visitor.blog.post.filter_by_category')]
    public function filterByCategory(
        Category $category,
        CategoryRepository $categoryRepository,
        TagRepository $tagRepository,
        PostRepository $postRepository
        ): Response
    {
        $categories = $categoryRepository->findAll();
        $tags = $tagRepository->findAll();
        $posts = $postRepository->getFilterPosts($category->getId());

        return $this->render('page/visitor/blog/index.html.twig', compact('categories', 'tags', 'posts'));

    }

    #[Route('/blog/{id<\d+>}/{slug}/filtre-par-tag', name: 'visitor.blog.post.filter_by_tag')]
    public function filterByTag(
        Tag $tag,
        CategoryRepository $categoryRepository,
        TagRepository $tagRepository,
        PostRepository $postRepository
        ): Response
    {
        $categories = $categoryRepository->findAll();
        $tags = $tagRepository->findAll();
        $posts = $postRepository->filterPostBytag($tag);

        // dd($posts);

        return $this->render('page/visitor/blog/index.html.twig', compact('categories', 'tags', 'posts'));

    }
}