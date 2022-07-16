<?php

namespace App\Controller;

use App\Data\SearchProduct;
use App\Entity\Category;
use App\Entity\Product;
use App\Entity\User;
use App\Form\UpdateProductType;
use App\Form\ProductType;
use App\Form\SearchProductType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends AbstractController
{
    #[Route('/product', name: 'app_product')]
    public function index(ManagerRegistry $doctrine, Request $request): Response
    {
        $search = new SearchProduct();
        $form = $this->createForm(SearchProductType::class, $search);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
           $products = $doctrine->getRepository(Product::class)->filterProduct($search);
        } else {
           $products = $doctrine->getRepository(Product::class)->findAll();
        }

        return $this->render('product/index.html.twig', [
            'products' => $products,
            'form' => $form->createView(),
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
    public function showProduct(Request $request, ManagerRegistry $doctrine, $id): Response
    {

        $em = $doctrine->getManager();

        $product = $doctrine->getRepository(Product::class)->find($id);


        
        // $form = $this->createForm(UpdateProductType::class, $product);
        // $form->handleRequest($request);

        // if ($form->isSubmitted() && $form->isValid()) {

        //     $image = $form->get('img')->getData();
        //     $imageName = md5(uniqid()) . '.' . $image->guessExtension();

        //     $image->move(
        //         // $this->getParameter permet de récupérer la valeur d'un paramètre définit dans le fichier de config services.yaml
        //         $this->getParameter('upload_file_product'),
        //         $imageName
        //     );
        //     $product->setImg($imageName);
        //     $product->setArtist($user);
        //     $product->setOrderItem(NULL);

        //     // On le persist et l'enregistre en BDD
        //    // $em->persist($product);
        //     $em->flush();

        //     $this->addFlash('success', 'Produit ajouté avec succes');

        //     return $this->redirectToRoute('home');
        // } else {
        //     $this->addFlash('error', 'Problème dans le formulaire');
        // }





        return $this->render('product/product.html.twig', [
            'product' => $product,
           // 'form' => $form->createView()
        ]);
    }
}
