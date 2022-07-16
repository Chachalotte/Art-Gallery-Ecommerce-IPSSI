<?php

namespace App\Controller;

use App\Entity\Category;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategoryController extends AbstractController
{
    #[Route('/categories', name: 'categories')]
    public function index(ManagerRegistry $doctrine): Response
    {        
        $categories = $doctrine->getRepository(Category::class)->findAll();

        return $this->render('category/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/category/{id}', name: 'category')]
    public function showCategory(ManagerRegistry $doctrine, $id): Response
    {
        $category = $doctrine->getRepository(Category::class)->find($id);

        return $this->render('category/category.html.twig', [
            'category' => $category,
        ]);
    }
}
