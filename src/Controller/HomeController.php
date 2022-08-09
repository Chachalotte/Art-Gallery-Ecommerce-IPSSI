<?php

namespace App\Controller;

use DateTime;
use App\Data\Mail;
use App\Entity\User;
use App\Entity\Event;
use App\Entity\Product;
use App\Form\EventType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $user = $doctrine->getRepository(User::class)->findAll();
        $productsToSell = $doctrine->getRepository(Product::class)->findBy(['isSold' => false]); 
        $productsSold = $doctrine->getRepository(Product::class)->findBy(['isSold' => true]);

        $artistArray = array();

        foreach ($user as $userInst) {
            //Récupération id de l'artiste
            $userId = $userInst->getId(); 
            $userRoles = $userInst->getRoles(); 

            if(in_array('ROLE_ARTIST', $userRoles))
            {
                $artistArray[$userId] = $userInst;
            }
        }

        return $this->render('home/index.html.twig', [
            // 'artists' => $artists,
            'artists' => $artistArray,
            'products' => $productsToSell,
            'solds' => $productsSold,
        ]);
    }

    #[Route('/events', name: 'events')]
    public function indexEvents(ManagerRegistry $doctrine): Response
    {
        $events = $doctrine->getRepository(Event::class)->findAll();

        return $this->render('home/indexEvents.html.twig', [
            'events' => $events,
        ]);
    }

    #[Route('/event/new/{id}', name: 'event_new')]
    public function newEvent(Request $request, ManagerRegistry $mr, $id): Response
    {
        if (!$this->getUser()->getRoles() == ['ROLE_ADMIN'] ) {
            return $this->redirectToRoute('home');
        }

        $em = $mr->getManager();
        $user = $em->getRepository(User::class)->find($id);

        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $image = $form->get('picture')->getData();
            $imageName = md5(uniqid()) . '.' . $image->guessExtension();

            $image->move(
                // $this->getParameter permet de récupérer la valeur d'un paramètre définit dans le fichier de config services.yaml
                $this->getParameter('upload_file_event'),
                $imageName
            );
            $event->setPicture($imageName);
            $event->setDatePublished(new DateTime('now'));

            // On le persist et l'enregistre en BDD
            $em->persist($event);
            $em->flush();

            $this->addFlash('success', 'Evènement ajouté avec succes');

            return $this->redirectToRoute('events');
        } else {
            $this->addFlash('error', 'Problème dans le formulaire');
        }

        return $this->render('home/newEvent.html.twig', [
            'form' => $form->createView()
        ]);
    }
}