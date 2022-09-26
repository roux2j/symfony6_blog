<?php

namespace App\Controller\Admin\Tag;

use App\Entity\Tag;
use App\Form\TagType;
use App\Repository\TagRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TagsController extends AbstractController
{
    #[Route('/administrateur/tags', name: 'admin.tag.index')]
    public function index(TagRepository $tagRepository): Response
    {
        $tags = $tagRepository->findAll();
        return $this->render('page/admin/tags/index.html.twig', compact('tags'));
    }

    #[Route('/administrateur/tags/insertion', name: 'admin.tag.create')]
    public function create(Request $request, TagRepository $tagRepository): Response
    {
        $tags = new Tag();

        $form = $this->createForm(TagType::class, $tags);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) 
        {
            $tagRepository->add($tags, true);
            $this->addFlash('success', 'votre tag a été créée ! ');
            return $this->redirectToRoute('admin.tag.index');
        }

        return $this->renderForm('page/admin/tags/create.html.twig', compact('form')); //retourne erreur 422 
    }

    #[Route('/administrateur/tag/{id<\d+>}/modification', name: 'admin.tag.edit')]
    public function edit(Tag $tag, Request $request, TagRepository $tagRepository)
    {
        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $tagRepository->add($tag, true);
            $this->addFLash("success", "ce tag a été modifiée avec succès");
            return $this->redirectToRoute('admin.tag.index');
        }

        return $this->renderForm('page/admin/tags/edit.html.twig', compact('form', 'tag'));
    }

    #[Route('/administrateur/tag/{id<\d+>}/suppression', name: 'admin.tag.delete')]
    public function delete($id, TagRepository $tagRepository, Request $request)
    {
        $tag = $tagRepository->findOneBy(array('id' => $id));

        if($this->isCsrfTokenValid('delete_category_' . $tag->getId(), $request->request->get('_token')))
        {
            $tagRepository->remove($tag,true);
            $this->addFlash('success', $tag->getName() . ' a été supprimée.');
        }
        return $this->redirectToRoute('admin.tag.index');
    }
}