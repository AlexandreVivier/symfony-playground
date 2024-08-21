<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('admin/category', name: 'admin.category.')]
class CategoryController extends AbstractController
{
    #[Route('', name: 'index')]
    public function index(CategoryRepository $repository): Response
    {
        $categories = $repository->findAll();
        return $this->render('category/index.html.twig', ['categories' => $categories]);
    }

    #[Route('/{slug}-{id}', name: 'show',  requirements: ['slug' => '[a-z0-9-]+', 'id' => '\d+'])]
    public function show(Category $category, CategoryRepository $repository): Response
    {
        $id = $category->getId();
        $category = $repository->find($id);
        if (!$category) {
            $this->addFlash('danger', 'Category not found');
            return $this->redirectToRoute('admin.category.index', [], Response::HTTP_MOVED_PERMANENTLY);
        }
        return $this->render('category/show.html.twig', ['category' => $category]);
    }

    #[Route('/new', name: 'new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($category);
            $em->flush();
            $this->addFlash('success', 'Category created successfully');
            return $this->redirectToRoute('admin.category.index');
        }
        return $this->render('category/new.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/{id}/edit', name: 'edit', requirements: ['id' => Requirement::DIGITS], methods: ['GET', 'POST'])]
    public function edit(Request $request, Category $category, EntityManagerInterface $em): Response
    {
        if (!$category) {
            $this->addFlash('danger', 'Category not found');
            return $this->redirectToRoute('admin.category.index', [], Response::HTTP_MOVED_PERMANENTLY);
        }
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Category updated successfully');
            return $this->redirectToRoute('admin.category.show', ['id' => $category->getId(), 'slug' => $category->getSlug()]);
        }
        return $this->render('category/edit.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/{id}', name: 'delete', requirements: ['id' => Requirement::DIGITS], methods: ['DELETE'])]
    public function delete(Category $category, EntityManagerInterface $em): Response
    {
        $em->remove($category);
        $em->flush();
        $this->addFlash('success', 'Category deleted successfully');
        return $this->redirectToRoute('admin.category.index');
    }
}
