<?php

namespace App\Controller;

use App\Entity\Artist;
use App\Entity\Product;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $artists = $doctrine->getRepository(Artist::class)->findAll();
        $products = $doctrine->getRepository(Product::class)->findAll();

        return $this->render('home/index.html.twig', [
            'artists' => $artists,
            'products' => $products,
        ]);
    }
}
