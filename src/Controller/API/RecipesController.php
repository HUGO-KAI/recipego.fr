<?php

namespace App\Controller\API;

use App\Entity\Recipe;
use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

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
    $recipes = $recipeRepository->findAll();
    return $this->json($recipes, 200, [], [
      'groups' => ['recipes.index', 'recipes.show']
    ]);
  }
  #[Route("/recipes/show/{id}", name: 'recipes_show_one', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
  public function showOne(RecipeRepository $recipeRepository, int $id): Response
  {
    $recipes = $recipeRepository->find($id);
    return $this->json($recipes, 200, [], [
      'groups' => ['recipes.index', 'recipes.show']
    ]);
  }
  #[Route("/recipes/create", name: 'recipes_create', methods: ['POST'])]
  public function create(RecipeRepository $recipeRepository, Request $request): Response
  {
    dump($request->getContent());
    return $this->json(
      ["message" => "Success"],
      200,
      ['content-type' => 'text/html']
    );
  }
}
