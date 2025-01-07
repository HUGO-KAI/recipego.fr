<?php

namespace App\Controller\API;

use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route("/api", name: 'api_', methods: ['GET'])]
class RecipesController extends AbstractController
{
  #[Route("/recipes", name: 'recipes', methods: ['GET'])]
  public function index(RecipeRepository $recipeRepository): Response
  {
    //$recipes = $recipeRepository->getAllRecipes();
    $recipes = $recipeRepository->findAll();
    return $this->json($recipes, 200, [], [
      'groups' => ['recipes.index']
    ]);
  }
  #[Route("/recipes/show", name: 'recipes_show', methods: ['GET'])]
  public function show(RecipeRepository $recipeRepository): Response
  {
    //$recipes = $recipeRepository->getAllRecipes();
    $recipes = $recipeRepository->findAll();
    return $this->json($recipes, 200, [], [
      'groups' => ['recipes.index', 'recipes.show']
    ]);
  }
}
