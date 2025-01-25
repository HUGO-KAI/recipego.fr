<?php

namespace App\Controller\API;

use App\Entity\Recipe;
use App\Repository\CategoryRepository;
use App\Repository\RecipeRepository;
use App\ValueResolver\Attribute\MapHydratedEntity;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route("/api", name: 'api_', methods: ['GET'])]
class RecipesController extends AbstractController
{
  //get all recipes name and category for page index
  #[Route("/recipes", name: 'recipes', methods: ['GET'])]
  public function index(RecipeRepository $recipeRepository): Response
  {
    //$recipes = $recipeRepository->getAllRecipes();
    $recipes = $recipeRepository->findAll();
    return $this->json($recipes, 200, [], [
      'groups' => ['recipes.index']
    ]);
  }
  //get all recipes for page show
  #[Route("/recipes/show", name: 'recipes_show', methods: ['GET'])]
  public function show(RecipeRepository $recipeRepository): Response
  {
    $recipes = $recipeRepository->findAll();
    return $this->json($recipes, 200, [], [
      'groups' => ['recipes.index', 'recipes.show']
    ]);
  }
  //get one recipe
  #[Route("/recipes/show/{id}", name: 'recipes_show_one', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
  public function showOne(RecipeRepository $recipeRepository, int $id): Response
  {
    $recipes = $recipeRepository->find($id);
    return $this->json($recipes, 200, [], [
      'groups' => ['recipes.index', 'recipes.show']
    ]);
  }
  //create a new recipe
  #[Route("/recipes/create", name: 'recipes_create', methods: ['POST'])]
  public function create(
    #[MapRequestPayload(serializationContext: ['groups' => ['write']])]
    Request $request,
    EntityManagerInterface $em,
    CategoryRepository $categoryRepository
  ): Response {
    //todo: save a new recipe
    $post = json_decode($request->getContent(), true);
    $recipe = new Recipe();
    $slugger = new AsciiSlugger();
    $recipe->setCreatedAt(new \DateTimeImmutable())
      ->setUpdatedAt(new \DateTimeImmutable())
      ->setSlug(strtolower($slugger->slug($post['title'])))
      ->setTitle($post['title'])
      ->setCategory($categoryRepository->findOneBy(['id' => $post['category']]))
      ->setContent($post['content']);
    $em->persist($recipe);
    $em->flush();
    return $this->json(
      ["message" => "Create success"],
      200,
      ['content-type' => 'text/html']
    );
  }
  //update a recipe
  #[Route("/recipes/update/{recipe}", name: 'recipes_update', methods: ['PUT'])]
  public function update(
    Recipe $recipe,
    EntityManagerInterface $em,
    CategoryRepository $categoryRepository,
    Request $request
  ): Response {
    $content = json_decode($request->getContent(), true);
    $slugger = new AsciiSlugger();
    $recipe->setTitle($content['title'])
      ->setSlug(strtolower($slugger->slug($content['title'])))
      ->setContent($content['content'])
      ->setCategory($categoryRepository->findOneBy(['id' => $content['category']]));
    $em->persist($recipe);
    $em->flush();
    return $this->json(
      ["message" => "Update success"],
      200,
      ['content-type' => 'text/html']
    );
  }
}
