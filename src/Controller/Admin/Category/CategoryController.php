<?php

namespace App\Controller\Admin\Category;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted("ROLE_ADMIN")]
class CategoryController extends AbstractController
{
    #[Route('/administrateur/categorie/liste', name: 'admin.category.index')]
    public function index(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();
        return $this->render('page/admin/category/index.html.twig', compact('categories'));
    }

    #[Route('/administrateur/categorie/insertion', name: 'admin.category.create')]
    public function create(Request $request, CategoryRepository $categoryRepository): Response
    {
        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) 
        {
            $categoryRepository->add($category, true);
            $this->addFlash('success', 'votre catégorie a été créée ! ');
            return $this->redirectToRoute('admin.category.index');
        }

        return $this->renderForm('page/admin/category/create.html.twig', compact('form')); //retourne erreur 422 
    }

    #[Route('/administrateur/categorie/{id<\d+>}/modification', name: 'admin.category.edit')]
    public function edit(Category $category, Request $request, CategoryRepository $categoryRepository)
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $categoryRepository->add($category, true);
            $this->addFLash("success", "cette categorie a été modifiée avec succès");
            return $this->redirectToRoute('admin.category.index');
        }

        return $this->renderForm('page/admin/category/edit.html.twig', compact('form', 'category'));
    }

    #[Route('/administrateur/categorie/{id<\d+>}/suppression', name: 'admin.category.delete')]
    public function delete($id, CategoryRepository $categoryRepository, Request $request)
    {
        $category = $categoryRepository->findOneBy(array('id' => $id));

        if($this->isCsrfTokenValid('delete_category_' . $category->getId(), $request->request->get('_token')))
        {
            $categoryRepository->remove($category,true);
            $this->addFlash('success', $category->getName() . ' a été supprimée.');
        }
        return $this->redirectToRoute('admin.category.index');
    }
}





