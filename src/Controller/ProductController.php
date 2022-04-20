<?php

namespace App\Controller;

use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

class ProductController extends AbstractController
{
    #[Route('/product', name: 'app_product')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $products = $doctrine->getRepository(Product::class);

        $products = $products->findAll();
        return $this->render('product/index.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/product/{id}', name: 'product')]
    public function showProduct(ManagerRegistry $doctrine, $id): Response
    {
        $product = $doctrine->getRepository(Product::class)->find($id);

        return $this->render('product/product.html.twig', [
            'product' => $product,
        ]);
    }
}
