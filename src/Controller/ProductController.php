<?php

namespace App\Controller;

use App\Data\SearchProduct;
use App\Entity\Category;
use App\Entity\Product;
use App\Entity\User;
use App\Entity\Comments;
use App\Form\UpdateProductType;
use App\Form\ProductType;
use App\Form\SearchProductType;
use App\Form\CommentType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends AbstractController
{
    #[Route('/products', name: 'app_product')]
    public function index(ManagerRegistry $doctrine, Request $request): Response
    {
        $search = new SearchProduct();
        $search->page = $request->get('page', 1);
        $form = $this->createForm(SearchProductType::class, $search);

        $form->handleRequest($request);
        
        $products = $doctrine->getRepository(Product::class)->filterProduct($search);

        return $this->render('product/index.html.twig', [
            'products' => $products,
            'form' => $form->createView(),
        ]);
    }
    
    #[Route('/products-sold', name: 'products_sold')]
    public function productSoldList(ManagerRegistry $doctrine, Request $request): Response
    {
        $search = new SearchProduct();
        $search->page = $request->get('page', 1);
        $form = $this->createForm(SearchProductType::class, $search);

        $form->handleRequest($request);
        $products = $doctrine->getRepository(Product::class)->filterSoldProduct($search);

        return $this->render('product/productsSold.html.twig', [
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
            $product->setIsSold(false);
            // On le persist et l'enregistre en BDD
            $em->persist($product);
            $em->flush();

            $this->addFlash('success', 'Produit ajouté avec succes');

            return $this->redirectToRoute('home');
        } elseif ($form->isSubmitted() && $form->isValid() == false) {
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
        $connectedUser = $this->getUser();
        $product = $doctrine->getRepository(Product::class)->find($id);
        $postedComments = $doctrine->getRepository(Comments::class)->findBy(['Product' => $id]);

        //Section ajout de commentaire
        $newComment = new Comments();
        $form = $this->createForm(CommentType::class, $newComment);
        $form->handleRequest($request);

        $products = $doctrine->getRepository(Product::class)->findAll();
        $productsother = [];
        foreach($products as $p){
            if((!$product->isSold() && !$p->isSold() && ($p->getArtist()->getId() != $product->getArtist()->getId())) || $product->isSold()){
                $productsother[] = $p;
            }
        }
        shuffle($productsother);
        $others = array_slice($productsother, 0, 4);

        if ($form->isSubmitted() && $form->isValid() && $connectedUser !== null) {

            $newComment->setProduct($product);
            $newComment->setUser($connectedUser);
            $newComment->setMessage($form['Message']->getData());

            // On le persist et l'enregistre en BDD
            $em->persist($newComment);
            $em->flush();
        }
        elseif ($form->isSubmitted() && $form->isValid() == false) {
            $this->addFlash('error', 'Problème dans le formulaire');
        }
        elseif ($form->isSubmitted() && $connectedUser == null) {
            $this->addFlash('error', 'Connectez-vous pour effectuer cette action');
        }


        return $this->render('product/product.html.twig', [
            'product' => $product,
            'comments' => $postedComments,
            'formComment' => $form->createView(),
            'others' => $others            
        ]);
    }

    #[Route('/product/comments/{id}', name: 'productComments')]
    public function showProductComments(Request $request, ManagerRegistry $doctrine, $id): Response
    {
        $em = $doctrine->getManager();
        $connectedUser = $this->getUser();
        $product = $doctrine->getRepository(Product::class)->find($id);
        $postedComments = $doctrine->getRepository(Comments::class)->findBy(['Product' => $id]);

        //Section ajout de commentaire
        $newComment = new Comments();
        $form = $this->createForm(CommentType::class, $newComment);
        $form->handleRequest($request);

        $products = $doctrine->getRepository(Product::class)->findAll();
        $productsother = [];
        foreach($products as $p){
            if(!$p->isSold() && ($p->getArtist()->getId() != $product->getArtist()->getId())){
                $productsother[] = $p;
            }
        }
        shuffle($productsother);
        $others = array_slice($productsother, 0, 4);

        if ($form->isSubmitted() && $form->isValid() && $connectedUser !== null) {

            $newComment->setProduct($product);
            $newComment->setUser($connectedUser);
            // $newComment->setDate(new \DateTime());
            $newComment->setMessage($form['Message']->getData());
            // $newComment->setMessage($form->getParameter('Message'));
            
            // On le persist et l'enregistre en BDD
            $em->persist($newComment);
            $em->flush();
            return $this->redirectToRoute('product', ['id'=> $id]);
        }
        elseif ($form->isSubmitted() && $form->isValid() == false) {
            $this->addFlash('error', 'Problème dans le formulaire');
        }
        elseif ($form->isSubmitted() && $connectedUser == null) {
            $this->addFlash('error', 'Connectez-vous pour effectuer cette action');
        }
        

        return $this->render('product/product.html.twig', [
            'product' => $product,
            'comments' => $postedComments,
            'formComment' => $form->createView(),
            'others' => $others  
        ]);
    }

    #[Route('/product/comments/edit/{id}/{idCom}', name: 'editComments')]
    public function editProductComment(Request $request, ManagerRegistry $doctrine, $id, $idCom): Response
    {
        $em = $doctrine->getManager();
        $connectedUser = $this->getUser();
        $product = $doctrine->getRepository(Product::class)->find($id);
        $editedComment = $doctrine->getRepository(Comments::class)->find($idCom);
        $postedComments = $doctrine->getRepository(Comments::class)->findBy(['Product' => $id]);

        $products = $doctrine->getRepository(Product::class)->findAll();
        $productsother = [];
        foreach($products as $p){
            if(!$p->isSold() && ($p->getArtist()->getId() != $product->getArtist()->getId())){
                $productsother[] = $p;
            }
        }
        shuffle($productsother);
        $others = array_slice($productsother, 0, 4);
        
        //Section ajout de commentaire
        $form = $this->createForm(CommentType::class, $editedComment);
        $form->handleRequest($request);

        $userRoles = $this->getUser()->getRoles(); 
        
        if ($editedComment->getUser() != $this->getUser() and in_array('ROLE_ADMIN', $userRoles) == false) {
            return $this->redirectToRoute('product', ['id'=> $id]);
        }

        if ($form->isSubmitted() && $form->isValid() && $connectedUser !== null) {

            // $editedComment->setProduct($product);
            // $editedComment->setUser($connectedUser);
             $editedComment->setMessage($form['Message']->getData());
            
            // $editedComment->setMessage("Franchement, non.");
            // On le persist et l'enregistre en BDD
            $em->persist($editedComment);
            $em->flush();
            return $this->redirectToRoute('product', ['id'=> $id]);
        }
        elseif ($form->isSubmitted() && $form->isValid() == false) {
            $this->addFlash('error', 'Problème dans le formulaire');
        }
        elseif ($form->isSubmitted() && $connectedUser == null) {
            $this->addFlash('error', 'Connectez-vous pour effectuer cette action');
        }
        

        return $this->render('product/product.html.twig', [
            'product' => $product,
            'comments' => $postedComments,
            'formComment' => $form->createView(),
            'others' => $others  
        ]);
    }

    #[Route('/product/comments/delete/{idCom}', name: 'deleteComments')]
    public function deleteProductComment(Request $request, ManagerRegistry $doctrine, $idCom): Response
    {
        $em = $doctrine->getManager();

        $comment = $doctrine->getRepository(Comments::class)->find($idCom);
        $userRoles = $this->getUser()->getRoles();


        if($comment && $comment->getUser() == $this->getUser() or in_array('ROLE_ADMIN', $userRoles)){
            $em->remove($comment);
            $em->flush();
        } else {
            $this->addFlash('error', "Vous n'avez pas les droits pour cette action");
            return $this->redirectToRoute('home');
        }

        return $this->redirectToRoute('product', ['id'=> $comment->getProduct()->getId()]);

    }


    #[Route('/product/edit/{id}', name: 'product_edit')]
    public function editProduct(Request $request, ManagerRegistry $doctrine, $id): Response
    {

        $em = $doctrine->getManager();

        $product = $doctrine->getRepository(Product::class)->find($id);

        if(!$product || $product->getArtist() != $this->getUser()){
            return $this->redirectToRoute('home');
        }
        
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $oldImg= $product->getImg();
            $image = $form->get('img')->getData();
            $imageName = md5(uniqid()) . '.' . $image->guessExtension();

            $image->move(
                // $this->getParameter permet de récupérer la valeur d'un paramètre définit dans le fichier de config services.yaml
                $this->getParameter('upload_file_product'),
                $imageName
            );
        

            // On le persist et l'enregistre en BDD
           // $em->persist($product);
           if($image){
            
            // $em->remove($oldImg);
           // $product->remove($this->getParameter('upload_file_product'), $oldImg);

            $product->setImg($imageName);
           }
           
            $em->flush();

            $this->addFlash('success', 'Produit modifié avec succes');

            return $this->redirectToRoute('home');
        }         
        elseif ($form->isSubmitted() && $form->isValid() == false) {
            $this->addFlash('error', 'Problème dans le formulaire');
        }
        elseif ($form->isSubmitted() && $connectedUser == null) {
            $this->addFlash('error', 'Connectez-vous pour effectuer cette action');
        }

        return $this->render('product/newProduct.html.twig', [
            'form' => $form->createView()

        ]);

    }


    #[Route('/product/delete/{id}', name: 'product_delete')]
    public function deleteProduct(Request $request, ManagerRegistry $doctrine, $id): Response
    {
        $em = $doctrine->getManager();

        $product = $doctrine->getRepository(Product::class)->find($id);

        if($product && $product->getArtist() == $this->getUser()){
            $em->remove($product);
            $em->flush();
        } else {
            $this->addFlash('error', "Vous n'avez pas les droits pour cette action");
            return $this->redirectToRoute('home');
        }

        return $this->redirectToRoute('artistPage', ['id'=> $product->getArtist()->getId()]);

    }


}
