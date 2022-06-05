<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $user = $doctrine->getRepository(User::class)->findAll();
        // $role = $user->getRoles();
        // $user = $doctrine->getRepository(User::class)->findArtist();
        // if ($user->getRoles() == ['ROLE_ARTIST']) {
        //     $artists = $user;
        // };
        // $artists = $doctrine->getRepository(User::class)->findBy(["roles" => ["ROLE_ARTIST"]], [], null, null);
        $products = $doctrine->getRepository(Product::class)->findAll();

        return $this->render('home/index.html.twig', [
            // 'artists' => $artists,
            'artists' => $user,
            'products' => $products,
        ]);
    }

    #[Route('/events', name: 'events')]
    public function indexEvents(ManagerRegistry $doctrine): Response
    {
        $events = $doctrine->getRepository(Even::class)->findAll();
        $products = $doctrine->getRepository(Product::class)->findAll();

        return $this->render('home/index.html.twig', [
            'events' => $events,
        ]);
    }
}
