<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\OrderItem;
use App\Repository\ProductRepository;
use function PHPUnit\Framework\isEmpty;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/cart', name: 'cart_')]
class CartController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(SessionInterface $session, ProductRepository $productRepository): Response
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('login');
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
        dump($session->get("cart"));

        return $this->render('cart/index.html.twig', compact("dataCart", "total"));
    }

    #[Route('/add/{id}', name: 'add')]
    public function add(Product $product, SessionInterface $session): Response
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('login');
        }

        $cart = $session->get("cart", []);
        $id = $product->getId();

        if (!empty($cart[$id])) {
            $cart[$id]++;
        } else {
            $cart[$id] = 1;
        }

        $session->set("cart", $cart);

        return $this->redirectToRoute("cart_index");
    }

    #[Route('/remove/{id}', name: 'remove')]
    public function remove(Product $product, SessionInterface $session): Response
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('login');
        }

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
        if(!$this->getUser()){
            return $this->redirectToRoute('login');
        }

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
        if(!$this->getUser()){
            return $this->redirectToRoute('login');
        }

        $session->set("cart", []);

        return $this->redirectToRoute("cart_index");
    }

    #[Route('/validateCart', name: 'validateCart')]
    public function validateCart(SessionInterface $session, ManagerRegistry $mr)
    {
        if(isEmpty($session->get("cart"))){
            return $this->redirectToRoute('cart_index');
        }
        
        $cart = $session->get("cart");
        $em = $mr->getManager();

        $orderItems = new OrderItems();
        $orderItems->addProdId();
        $orderItems->
        
        // foreach($cart as $productId => $quantity){
        //     $product = $mr->getRepository(Product::class)->find($productId);
        //     $product->addOrderItem();
        // }

        $em->persist($orderItems);
        $em->flush();
    }
}
