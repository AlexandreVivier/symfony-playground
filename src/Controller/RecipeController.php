<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
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
            $this->addFlash('danger', 'Recette introuvable');
            return $this->redirectToRoute('recipe.index', [], Response::HTTP_MOVED_PERMANENTLY);
        }
        return $this->render('recipe/show.html.twig', ['recipe' => $recipe]);
    }

    #[Route('/recettes/{id}/edit', name: 'recipe.edit', requirements: ['id' => '\d+'])]
    public function edit(Request $request, Recipe $recipe, RecipeRepository $repository, EntityManagerInterface $em): Response
    {
        if (!$recipe) {
            $this->addFlash('danger', 'Recette introuvable');
            return $this->redirectToRoute('recipe.index', [], Response::HTTP_MOVED_PERMANENTLY);
        }

        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request); // le handleRequest set par défaut tous les champs avec les nouvelles valeurs transmises
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Recette modifiée avec succès');
            return $this->redirectToRoute('recipe.show', ['id' => $recipe->getId(), 'slug' => $recipe->getSlug()]);
        }
        return $this->render('recipe/edit.html.twig', ['recipe' => $recipe, 'form' => $form->createView()]);
    }

    #[Route('/recettes/new', name: 'recipe.new')]
    public function new(Request $request, RecipeRepository $repository, EntityManagerInterface $em): Response
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);

        // On set la date de création et update à maintenant :
        $recipe->setCreatedAt(new \DateTimeImmutable());
        $recipe->setUpdatedAt(new \DateTimeImmutable());

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // On prend le titre qu'on sépare par des - pour set le slug :
            $slug = $recipe->getTitle();
            // // Convertir en minuscules
            $slug = strtolower($slug);
            // // Supprimer les accents
            $slug = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $slug);
            // // Remplacer les caractères non désirés par des tirets
            // TODO changer les caracs par des lettres
            $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
            // // Supprimer les tirets en début et fin de chaîne
            $slug = trim($slug, '-');
            $recipe->setSlug($slug);
            // dd($slug);
            $em->persist($recipe);
            $em->flush();
            $this->addFlash('success', 'Recette ajoutée avec succès');
            return $this->redirectToRoute('recipe.show', ['id' => $recipe->getId(), 'slug' => $recipe->getSlug()]);
        }
        return $this->render('recipe/new.html.twig', ['form' => $form->createView()]);
    }
}
