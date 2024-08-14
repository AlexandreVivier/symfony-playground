<?php

namespace App\Controller;

use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RecipeController extends AbstractController
{
    #[Route('/recettes', name: 'recipe.index')]
    public function index(Request $request, RecipeRepository $repository, EntityManagerInterface $em): Response
    {
        $recipes = $repository->findAll();
        return $this->render('recipe/index.html.twig', ['recipes' => $recipes]);
    }

    #[Route('/recettes/{slug}-{id}', name: 'recipe.show', requirements: ['slug' => '[a-z0-9-]+', 'id' => '\d+'])]
    public function show(Request $request, String $slug, Int $id, RecipeRepository $repository): Response
    {
        $recipe = $repository->find($id);
        if (!$recipe || $recipe->getSlug() !== $slug) {
            return $this->redirectToRoute('recipe.index', [], Response::HTTP_MOVED_PERMANENTLY);
        }
        return $this->render('recipe/show.html.twig', ['recipe' => $recipe]);
    }
}
