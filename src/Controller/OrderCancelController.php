<?php

namespace App\Controller;

use App\Entity\Order;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderCancelController extends AbstractController
{
    #[Route('commande/erreur/{stripeSessionId}', name: 'order_cancel')]
    public function index(ManagerRegistry $doctrine, $stripeSessionId): Response
    {
        $order = $doctrine->getRepository(Order::class)->findOneBy(['stripeSessionId' => $stripeSessionId]);

        if(!$order || $order->getUser() != $this->getUser()){
            return $this->redirectToRoute('home');
        }

        //envoie email
        
        return $this->render('order_cancel/index.html.twig', [
            'order' => $order,
        ]);
    }
}
