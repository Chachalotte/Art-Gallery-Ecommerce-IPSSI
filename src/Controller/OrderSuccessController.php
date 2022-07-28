<?php

namespace App\Controller;

use App\Data\Mail;
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

        if($order->getState() == 0){
            $session->set("cart", []);
            $order->setState(1);
            $doctrine->getManager()->flush();

            //envoi d'email mailjet
            $mail = new Mail();
            $content = $order->getUser()->getFirstname().' '.$order->getUser()->getName().", merci pour votre achat !<br/>Votre commande The art Factory est bien validée";
            $mail->send($order->getUser()->getEmail(), $order->getUser()->getFirstname().' '.$order->getUser()->getName(), 'Votre commande The art Factory est bien validée', $content);
        }

        return $this->render('order_success/index.html.twig', [
            'order' => $order,
        ]);
    }
}
