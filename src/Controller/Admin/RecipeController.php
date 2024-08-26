<?php

namespace App\Controller\Admin;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/admin/recettes', name: 'admin.recipe.')]
class RecipeController extends AbstractController
{
    #[Route('', name: 'index')]
    public function index(RecipeRepository $repository): Response
    {
        $recipes = $repository->findWithDurationLowerThan(30);

        return $this->render('recipe/index.html.twig', ['recipes' => $recipes]);
    }

    #[Route('/{slug}-{id}', name: 'show', requirements: ['slug' => '[a-z0-9-]+', 'id' => '\d+'])]
    public function show(String $slug, Int $id, RecipeRepository $repository): Response
    {
        $recipe = $repository->find($id);
        if (!$recipe || $recipe->getSlug() !== $slug) {
            $this->addFlash('danger', 'Recette introuvable');
            return $this->redirectToRoute('admin.recipe.index', [], Response::HTTP_MOVED_PERMANENTLY);
        }
        return $this->render('recipe/show.html.twig', ['recipe' => $recipe]);
    }

    #[Route('/{id}/edit', name: 'edit', requirements: ['id' => Requirement::DIGITS], methods: ['GET', 'POST'])]
    public function edit(Request $request, Recipe $recipe, EntityManagerInterface $em): Response
    {
        if (!$recipe) {
            $this->addFlash('danger', 'Recette introuvable');
            return $this->redirectToRoute('admin.recipe.index', [], Response::HTTP_MOVED_PERMANENTLY);
        }

        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request); // le handleRequest set par défaut tous les champs avec les nouvelles valeurs transmises
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $file */
            $file = $form->get('thumbnailFile')->getData();
            $filename = $recipe->getId() . '.' . $file->getClientOriginalExtension();
            $file->move($this->getParameter('kernel.project_dir') . '/public/recettes/images', $filename);
            $recipe->setThumbnail($filename);
            $em->flush();
            $this->addFlash('success', 'Recette modifiée avec succès');
            return $this->redirectToRoute('admin.recipe.show', ['id' => $recipe->getId(), 'slug' => $recipe->getSlug()]);
        }
        return $this->render('recipe/edit.html.twig', ['recipe' => $recipe, 'form' => $form->createView()]);
    }

    #[Route('/new', name: 'new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($recipe);
            $em->flush();
            $this->addFlash('success', 'Recette ajoutée avec succès');
            return $this->redirectToRoute('admin.recipe.show', ['id' => $recipe->getId(), 'slug' => $recipe->getSlug()]);
        }
        return $this->render('recipe/new.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/{id}', name: 'delete', requirements: ['id' => Requirement::DIGITS], methods: ['DELETE'])]
    public function delete(Recipe $recipe, EntityManagerInterface $em): Response
    {
        if (!$recipe) {
            $this->addFlash('danger', 'Recette introuvable');
            return $this->redirectToRoute('admin.recipe.index', [], Response::HTTP_MOVED_PERMANENTLY);
        }
        $em->remove($recipe);
        $em->flush();
        $this->addFlash('success', 'Recette supprimée avec succès');
        return $this->redirectToRoute('admin.recipe.index');
    }
}
