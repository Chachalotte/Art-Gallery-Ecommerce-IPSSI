<?php

namespace App\Controller;

use App\Entity\Order;
use App\Form\OrderType;
use App\Entity\OrderDetails;
use App\Repository\ProductRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderController extends AbstractController
{
    #[Route('/commande', name: 'order')]
    public function index(SessionInterface $session, ProductRepository $productRepository, Request $request): Response
    {
        if(!$this->getUser()->getAdresses()->getValues()){
            return $this->redirectToRoute('new_address');
        }

        $cart = $session->get("cart", []);
        $dataCart = [];
        $total = 0;
        foreach ($cart as $id => $quantity) {
            $product = $productRepository->find($id);
            $dataCart[] = [
                "product" => $product,
                "quantity" => $quantity
            ];
            $total += $product->getPrice() * $quantity;
        }

        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser()
        ]);

        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
            'cart' => $dataCart
        ]);
    }

    #[Route('/commande/recapitulatif', name: 'order_recap', methods:"POST")]
    public function add(SessionInterface $session, ProductRepository $productRepository, Request $request, ManagerRegistry $doctrine): Response
    {
        if(!$this->getUser()->getAdresses()->getValues()){
            return $this->redirectToRoute('new_address');
        }

        $em = $doctrine->getManager();
        $cart = $session->get("cart", []);
        $dataCart = [];
        $total = 0;
        foreach ($cart as $id => $quantity) {
            $product = $productRepository->find($id);
            $dataCart[] = [
                "product" => $product,
                "quantity" => $quantity
            ];
            $total += $product->getPrice() * $quantity;
        }

        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser()
        ]);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $date = new \DateTime();
            $carrier = $form->get('carriers')->getData();
            $delivery = $form->get('addresses')->getData();
            $delivery_content = $delivery->getFirstname().' '.$delivery->getLastname();
            $delivery_content .= '<br/>'.$delivery->getPhone();

            if($delivery->getCompany()){
                $delivery_content .= '<br/>'.$delivery->getCompany();
            }

            $delivery_content .= '<br/>'.$delivery->getAddress();
            $delivery_content .= '<br/>'.$delivery->getPostal().' '.$delivery->getCity();
            $delivery_content .= '<br/>'.$delivery->getCountry();

            $order = new Order();
            $order->setUser($this->getUser());
            $order->setCreatedAt($date);
            $order->setCarrierName($carrier->getName());
            $order->setCarrierPrice($carrier->getPrice());
            $order->setDelivery($delivery_content);
            $order->setIsPaid(0);

            $em->persist($order);
            
            foreach($dataCart as $product){
                $orderDetails = new OrderDetails();
                $orderDetails->setMyOrder($order);
                $orderDetails->setProduct($product['product']->getName());
                $orderDetails->setPrice($product['product']->getPrice());
                $orderDetails->setTotal($product['product']->getPrice());
                $em->persist($orderDetails);
            }
            $em->flush();

            return $this->render('order/add.html.twig', [
                'form' => $form->createView(),
                'cart' => $dataCart,
                'total' => $total,
                'carrier' => $carrier,
                'delivery' => $delivery_content
            ]);
        }     
        
        return $this->redirectToRoute('home');
    }
}

