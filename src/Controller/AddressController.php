<?php

namespace App\Controller;

use App\Entity\Adress;
use App\Form\AddressType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AddressController extends AbstractController
{
    #[Route('/adresses', name: 'adresses')]
    public function index(): Response
    {
        return $this->render('users/profil/address.html.twig');
    }

    #[Route('/nouvelle_adresse', name: 'new_address')]
    public function add(SessionInterface $session, Request $request, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        
        $address = new Adress();
        $form = $this->createForm(AddressType::class, $address);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $address->setUser($this->getUser());
            $em->persist($address);
            $em->flush();

            if($session->get("cart")){
                return $this->redirectToRoute('order');
            } else {
                return $this->redirectToRoute('app_profil', ['id' => $this->getUser()->getId()]);
            }
        }

        return $this->render('users/profil/address_new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/edit_adresse/{id}', name: 'edit_address')]
    public function edit(Request $request, ManagerRegistry $doctrine, $id): Response
    {
        $em = $doctrine->getManager();
        
        $address = $doctrine->getRepository(Adress::class)->find($id);

        if(!$address || $address->getUser() != $this->getUser()){
            return $this->redirectToRoute('app_profil', ['id' => $this->getUser()->getId()]);
        }
        $form = $this->createForm(AddressType::class, $address);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            
            $em->flush();

            return $this->redirectToRoute('app_profil', ['id' => $this->getUser()->getId()]);
        }

        return $this->render('users/profil/address_new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/delete_adresse/{id}', name: 'delete_address')]
    public function delete(ManagerRegistry $doctrine, $id): Response
    {
        $em = $doctrine->getManager();
        
        $address = $doctrine->getRepository(Adress::class)->find($id);

        if($address && $address->getUser() == $this->getUser()){
            $em->remove($address);   
            $em->flush();
        }    
            
        return $this->redirectToRoute('app_profil', ['id' => $this->getUser()->getId()]);
        
    }
}
