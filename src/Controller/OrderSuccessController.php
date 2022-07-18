<?php

namespace App\Controller;

use App\Entity\Order;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderSuccessController extends AbstractController
{
    #[Route('commande/succes/{stripeSessionId}', name: 'order_validate')]
    public function index(ManagerRegistry $doctrine, SessionInterface $session, $stripeSessionId): Response
    {
        $order = $doctrine->getRepository(Order::class)->findOneBy(['stripeSessionId' => $stripeSessionId]);

        if(!$order || $order->getUser() != $this->getUser()){
            return $this->redirectToRoute('home');
        }

        if(!$order->getIsPaid()){
            $session->set("cart", []);
            $order->setIsPaid(1);
            $doctrine->getManager()->flush();
        }
        //Envoie d'email au client pour confirmer la commande

        return $this->render('order_success/index.html.twig', [
            'order' => $order,
        ]);
    }
}
