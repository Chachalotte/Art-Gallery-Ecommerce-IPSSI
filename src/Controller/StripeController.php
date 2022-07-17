<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Product;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Repository\ProductRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StripeController extends AbstractController
{
    #[Route('/commande/create-session/{reference}', name: 'stripe_create_session')]
    public function index(SessionInterface $session, ProductRepository $productRepository, ManagerRegistry $doctrine, $reference)
    {
        $products_for_stripe = [];
        $YOUR_DOMAIN = 'http://127.0.0.1:8000/';
        dump($reference);
        $order = $doctrine->getRepository(Order::class)->findOneBy(['reference' => $reference]);
        dump($doctrine->getRepository(Order::class)->findOneBy(['reference' => $reference]));
        if(!$order){
            new JsonResponse(['error' => 'order']);
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

        foreach($order->getOrderDetails()->getValues() as $product){
            $product_object = $doctrine->getRepository(Product::class)->findOneBy(['name' => $product->getProduct()]);
            $products_for_stripe[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $product->getPrice() * 100,
                    'product_data' => [
                        'name' => $product->getProduct(),
                        'images' => [$YOUR_DOMAIN."/img/product/".$product_object->getImg()],
                    ],
                ],
                'quantity' => 1,
            ];
        }

        $products_for_stripe[] = [
            'price_data' => [
                'currency' => 'eur',
                'unit_amount' => $order->getCarrierPrice() * 100,
                'product_data' => [
                    'name' => $order->getCarrierName(),
                    'images' => [$YOUR_DOMAIN],
                ],
            ],
            'quantity' => 1,
        ];

        Stripe::setApiKey('sk_test_51LMbemHvaioc7A6SVwKhoyxshxbVqQHL3rtGOSNvrEWhuq7BgI78Dd1hZwe6221FKQKCa5R4X3J9ryFpyogEaCwo00L70sahHy');
            

        $checkout_session = Session::create([
            'customer_email' => $this->getUser()->getEmail(),
            'payment_method_types' => ['card'],    
            'line_items' => [[
                $products_for_stripe
            ]],
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . '/success.html',
            'cancel_url' => $YOUR_DOMAIN . '/cancel.html',
        ]);

        $response =new JsonResponse(['id' => $checkout_session->id]);
        return $response;
    }
}
