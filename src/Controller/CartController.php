<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cart', name: 'cart_')]
class CartController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(SessionInterface $session, ProductRepository $productRepository): Response
    {
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

        return $this->render('cart/index.html.twig', compact("dataCart", "total"));
    }

    #[Route('/add/{id}', name: 'add')]
    public function add(Product $product, SessionInterface $session): Response
    {
        $cart = $session->get("cart", []);
        $id = $product->getId();

        if (!empty($cart[$id])) {
            $cart[$id]++;
        } else {
            $panier[$id] = 1;
        }

        $session->set("cart", $cart);

        return $this->redirectToRoute("cart_index");
    }

    #[Route('/remove/{id}', name: 'remove')]
    public function remove(Product $product, SessionInterface $session): Response
    {
        $cart = $session->get("cart", []);
        $id = $product->getId();

        if (!empty($cart[$id])) {
            if ($cart[$id] > 1) {
                $cart[$id]--;
            } else {
                unset($cart[$id]);
            }
        }

        $session->set("cart", $cart);

        return $this->redirectToRoute("cart_index");
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function delete(Product $product, SessionInterface $session): Response
    {
        $cart = $session->get("cart", []);
        $id = $product->getId();

        if (!empty($cart[$id])) {
            unset($cart[$id]);
        }

        $session->set("cart", $cart);

        return $this->redirectToRoute("cart_index");
    }

    #[Route('/deleteAll', name: 'deleteAll')]
    public function deleteAll(SessionInterface $session): Response
    {
        $session->set("cart", []);

        return $this->redirectToRoute("cart_index");
    }
}
