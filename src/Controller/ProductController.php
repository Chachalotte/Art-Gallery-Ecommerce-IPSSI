<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\User;
use App\Form\ProductType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends AbstractController
{
    #[Route('/product', name: 'app_product')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $products = $doctrine->getRepository(Product::class)->findAll();

        return $this->render('product/index.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/product/new/{id}', name: 'product_new')]
    public function NewProduct(Request $request, ManagerRegistry $mr, $id): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('login');
        }

        $em = $mr->getManager();
        $user = $em->getRepository(User::class)->find($id);

        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()->getRoles() == ['ROLE_USER']) {
                $user->setRoles(['ROLE_ARTIST']);
            }
            $image = $form->get('img')->getData();
            $imageName = md5(uniqid()) . '.' . $image->guessExtension();

            $image->move(
                // $this->getParameter permet de récupérer la valeur d'un paramètre définit dans le fichier de config services.yaml
                $this->getParameter('upload_file_product'),
                $imageName
            );
            $product->setImg($imageName);
            $product->setArtist($user);
            $product->setOrderItem(NULL);

            // On le persist et l'enregistre en BDD
            $em->persist($product);
            $em->flush();

            $this->addFlash('success', 'Produit ajouté avec succes');

            return $this->redirectToRoute('home');
        } else {
            $this->addFlash('error', 'Problème dans le formulaire');
        }

        return $this->render('product/newProduct.html.twig', [
            'form' => $form->createView()

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
