<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use App\Repository\RecipeRepository;
use App\Repository\QuantityRepository;
use Doctrine\ORM\EntityManagerInterface;

class RecipeController extends AbstractController
{
  //show all recipes
  #[Route('/recettes', name: 'recipe.index')]
  public function getAllRecettes(Request $request, RecipeRepository $RecipeRepository, EntityManagerInterface $em): Response
  {

    //$recipes = $RecipeRepository->findWithDurationLowerThan(30);
    $offset = max(0, $request->query->getInt('offset', 0));
    $paginator = $RecipeRepository->getRecipePaginator($offset);
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
    return $this->render('recipe/index.html.twig', [
      'recipes' => $paginator,
      'previous' => $offset - RecipeRepository::RECIPES_PER_PAGE,
      'next' => min(count($paginator), $offset + RecipeRepository::RECIPES_PER_PAGE),
    ]);
  }
  //show one recipe for all users
  #[Route('/recettes/{slug}-{id}', name: 'recipe.show', requirements: ['id' => '\d+', 'slug' => '[a-z0-9-]+'])]
  public function show(string $slug, int $id, RecipeRepository $repository, QuantityRepository $quantityRepository): Response
  {
    $recipe = $repository->find($id);
    $result = $quantityRepository->findAll();
    $ingredients = [];
    foreach ($result as $quantity) {
      if ($quantity->getRecipe()->getId() == $recipe->getId()) {
        array_push($ingredients, $quantity);
      }
    }
    if (
      empty($recipe) || $slug !== $recipe->getSlug()
    ) {
      return $this->redirectToRoute('recipe.show', ['slug' => $recipe->getSlug(), 'id' => $recipe->getId()]);
    }
    return $this->render('recipe/show.html.twig', [
      'recipe' => $recipe,
      'ingredients' => $ingredients
    ]);
  }
}
