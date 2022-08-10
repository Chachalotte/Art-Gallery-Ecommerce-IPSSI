<?php

namespace App\Controller;
use App\Entity\Contact;
use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact')]
    public function index(Request $request, ManagerRegistry $mr)
    {
        $em = $mr->getManager();

        $contact = new Contact();

        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $contact->setDate(new \DateTime());
            $contact->setStatut('Posté');

            $em->persist($contact);
            $em->flush();

            $this->addFlash('success', 'Merci de nous avoir contacté. Notre équipe va vous répondre dans les plus brèves délais.');

            return $this->redirectToRoute('home');
        }
        elseif ($form->isSubmitted() && $form->isValid() == false){
            $this->addFlash('error', 'Un problème est survenu dans le traitement du formulaire.');
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
