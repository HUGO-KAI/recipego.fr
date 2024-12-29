<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/admin', name: 'admin_')]
class CategoryController extends AbstractController
{
    //show one category
    #[Route('/categories/{slug}-{id}', name: 'category.show', requirements: ['id' => '\d+', 'slug' => '[a-z0-9-]+'])]
    public function show(string $slug, int $id, CategoryRepository $repository): Response
    {
        $category = $repository->find($id);
        if (empty($category) || $slug !== $category->getSlug()) {
            return $this->redirectToRoute('admin_recipe.show', ['slug' => $category->getSlug(), 'id' => $category->getId()]);
        }
        return $this->render('admin/category/show.html.twig', [
            'category' => $category
        ]);
    }
    //add one category
    #[Route('/categories/create', name: 'category.create')]
    public function add(Request $request, EntityManagerInterface $em): Response //symfony va chercher symtematiquement un category selon id
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($category);
            $em->flush();
            $this->addFlash(
                'success',
                'La catégorie a bien été créée!'
            );
            return $this->redirectToRoute('admin_index');
        }
        return $this->render('admin/category/create.html.twig', [
            'form' => $form,
        ]);
    }
    //modify one category
    #[Route('/categories/{id}/edit', name: 'category.edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Request $request, Category $category, EntityManagerInterface $em): Response //symfony va chercher symtematiquement un category selon id
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash(
                'success',
                'La recette a bien été modifié!'
            );
            return $this->redirectToRoute('admin_index');
        }
        return $this->render('admin/category/edit.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }
    //remove one category
    #[Route('/categories/{id}/remove', name: 'category.remove', requirements: ['id' => '\d+'])]
    public function remove(Category $category, EntityManagerInterface $em): Response //symfony va chercher symtematiquement un category selon id
    {
        if ($category) {
            $em->remove($category);
            $em->flush();
            $this->addFlash(
                'success',
                'La recette a été supprimée!'
            );
            return $this->redirectToRoute('admin_index');
        } else {
            $response = new Response("La recette n'existe pas");
            $response->setStatusCode(404);
            return $this->redirectToRoute('admin_index');
        }
    }
}
