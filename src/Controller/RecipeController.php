<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RecipeController extends AbstractController
{
    #[Route('/recettes', name: 'recipe.index')]
    public function index(Request $request): Response
    {
        return new Response('Liste des recettes');
    }

    #[Route('/recettes/{slug}-{id}', name: 'recipe.show', requirements: ['slug' => '[a-z0-9-]+', 'id' => '\d+'])]
    public function show(Request $request, String $slug, Int $id): Response
    {
        // dd($slug, $id);
        return new JsonResponse(['slug' => $slug, 'id' => $id]);
        // return new Response('Recette ' . $slug . ' - n°' . $id);
    }
}
