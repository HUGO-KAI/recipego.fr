<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;

class RecipeController extends AbstractController
{
    //show all recipes
    #[Route('/recettes', name: 'recipe.index')]
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
        return $this->render('recipe/index.html.twig', [
            'recipes' => $recipes
        ]);
    }
    //show one recipe
    #[Route('/recettes/{slug}-{id}', name: 'recipe.show', requirements: ['id' => '\d+', 'slug' => '[a-z0-9-]+'])]
    public function show(string $slug, int $id, RecipeRepository $repository): Response
    {
        $recipe = $repository->find($id);
        if (
            empty($recipe) || $slug !== $recipe->getSlug()
        ) {
            return $this->redirectToRoute('recipe.show', ['slug' => $recipe->getSlug(), 'id' => $recipe->getId()]);
        }
        return $this->render('recipe/show.html.twig', [
            'recipe' => $recipe
        ]);
    }
}
