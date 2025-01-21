<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Entity\User;
use App\Form\RecipeType;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/admin', name: 'admin_')]
class AdminController extends AbstractController
{
  #[Route('/index', name: 'index')]
  public function index(RecipeRepository $repoRecipe, CategoryRepository $categoryRecipe, EntityManagerInterface $em): Response
  {
    //how to get current user
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    $user = $this->getUser();
    $role = $user->getRoles()['0'];
    $recipes = [];
    $categories = [];
    if ($role == 'ROLE_USER') {
      $recipes = $repoRecipe->findByUserId($user);
    } elseif ($role == 'ROLE_ADMIN') {
      $recipes = $repoRecipe->findAll();
      $categories = $categoryRecipe->findAll();
    }
    return $this->render('admin/index.html.twig', [
      'recipes' => $recipes,
      'categories' => $categories
    ]);
  }

  //show all recipes in admin
  #[Route('/recettes', name: 'recipe.getAllRecettes')]
  public function getAllRecettes(Request $request, RecipeRepository $repository, EntityManagerInterface $em): Response
  {
    $recipes = $repository->findAll();
    //create recipe
    /* $recipe = new Recipe();
        $recipe->setTitle('Barbe à papa')
            ->setSlug('barbe-papa')
            ->setContent('Infos pratiques
                        Nombre de personnes4
                        Quantité500g de sucre
                        Temps de préparation2 minutes
                        Degré de difficultéTrès facile
                        Les ingrédients de la recette
                        500g de sucre en poudre pour
                        5 grande cuillère à café de colorant parfumé Flossine')
            ->setDuration(2)
            ->setCreatedAt(new \DateTimeImmutable())
            ->setUpdatedAt(new \DateTimeImmutable());
        $em->persist($recipe);
        $em->flush(); */
    return $this->render('admin/recipe/index.html.twig', [
      'recipes' => $recipes
    ]);
  }
  //show one recipe in admin
  #[Route('/recettes/{slug}-{id}', name: 'recipe.show', requirements: ['id' => '\d+', 'slug' => '[a-z0-9-]+'])]
  public function show(string $slug, int $id, RecipeRepository $repository): Response
  {
    $recipe = $repository->find($id);
    if (empty($recipe) || $slug !== $recipe->getSlug()) {
      return $this->redirectToRoute('admin_recipe.show', ['slug' => $recipe->getSlug(), 'id' => $recipe->getId()]);
    }
    return $this->render('admin/recipe/show.html.twig', [
      'recipe' => $recipe
    ]);
  }
  //add one recipe
  #[Route('/recettes/create', name: 'recipe.create')]
  public function add(Request $request, EntityManagerInterface $em): Response //symfony va chercher symtematiquement un recipe selon id
  {
    $recipe = new Recipe();
    $form = $this->createForm(RecipeType::class, $recipe);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
      $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
      $user = $this->getUser();
      $recipe->setUser($user);
      $em->persist($recipe);
      $em->flush();
      $this->addFlash(
        'success',
        'La recette a bien été créée!'
      );
      return $this->redirectToRoute('admin_index');
    }
    return $this->render('admin/recipe/create.html.twig', [
      'form' => $form,
    ]);
  }
  //modify one recipe
  #[Route('/recettes/{id}/edit', name: 'recipe.edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
  public function edit(Request $request, Recipe $recipe, EntityManagerInterface $em): Response //symfony va chercher symtematiquement un recipe selon id
  {
    $form = $this->createForm(RecipeType::class, $recipe);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
      $user = $this->getUser();
      //Si user n'est pas enregistré, set current user
      if (empty($recipe->getUser())) {
        $recipe->setUser($user);
      }
      $em->flush();
      $this->addFlash(
        'success',
        'La recette a bien été modifié!'
      );
      return $this->redirectToRoute('admin_index');
    }
    return $this->render('admin/recipe/edit.html.twig', [
      'recipe' => $recipe,
      'form' => $form,
    ]);
  }
  //remove one recipe
  #[Route('/recettes/{id}/remove', name: 'recipe.remove', requirements: ['id' => '\d+'])]
  public function remove(Recipe $recipe, EntityManagerInterface $em): Response //symfony va chercher symtematiquement un recipe selon id
  {
    if ($recipe) {
      $em->remove($recipe);
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
