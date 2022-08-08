<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Order;
use App\Entity\Comments;
use App\Entity\Subscriptions;
use App\Form\ProfilType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ProfilController extends AbstractController
{
     //=============================================
    //          Page de profil de l'utilisateur
    //=============================================
    #[Route('/profil/{id<\d+>}', name: 'app_profil')]
    public function Profil(ManagerRegistry $doctrine,  Request $request, UserPasswordHasherInterface $userPasswordHasher, $id)
    {
        $em = $doctrine->getManager();

        $user = $em->getRepository(User::class)->find($id);
        if ($this->getUser() != $user) {
            return $this->createAccessDeniedException();
        }

        // $comment = $user->getComments();
        $follows = $doctrine->getRepository(Subscriptions::class)->findBy(['User' => $id]);
        $orders = $doctrine->getRepository(Order::class)->findSuccessOrders($this->getUser()); 
        $comments = $doctrine->getRepository(Comments::class)->findBy(['User' => $id]); 

        $form = $this->createForm(ProfilType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('plainEmail')->getData() !== null) {
                $user->setEmail($form['plainEmail']->getData());
            } else {
                $user->setEmail($oldUser->getEmail());
            }

            if ($form->get('firstname')->getData() !== null) {
                $user->setFirstname($form['firstname']->getData());
            } else {
                $user->setFirstname($oldUser->getFirstname());
            }

            if ($form->get('name')->getData() !== null) {
                $user->setName($form['name']->getData());
            } else {
                $user->setName($oldUser->getName());
            }

            if ($form->get('gender')->getData() !== null) {
                $user->setGender($form['gender']->getData());
            } else {
                if ($oldUser->getGender() !== null) {
                    $user->setGender($oldUser->getGender());
                } else {
                    $user->setGender(null);
                }
            }

            if ($form->get('age')->getData() !== null) {
                $user->setAge($form['age']->getData());
            } else {
                $user->setAge($oldUser->getAge());
            }

            if ($form->get('plainPassword')->getData() !== null) {
                $user->setPassword(
                    $userPasswordHasher->hashPassword($user, $form['plainPassword']->getData())
                );
            } else {
                $user->setPassword($oldUser->getPassword());
            }

            if ($form->get('avatar')->getData() !== null) {
                $avatar = $form->get('avatar')->getData();
                $avatarName = md5(uniqid()) . '.' . $avatar->guessExtension();

                $avatar->move(
                    $this->getParameter('upload_file_user'),
                    $avatarName
                );
                $user->setAvatar($avatarName);
            } else {
                // $user->setAvatar($oldUser->getAvatar());
            }

            
            $em->persist($user);
            $em->flush();

            $this->addFlash("success", "Le profil a bien été modifié");

            return $this->redirectToRoute("app_profil", ['id' => $user->getId()]);
        }

        return $this->render('users/profil/profil.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'comments' => $comments,
            'follows' => $follows,
            'orders' => $orders,
        ]);
    }

    #[Route('/profil/{id<\d+>}/order/{reference}', name: 'profil_order_show')]
    public function show(ManagerRegistry $doctrine, $id, $reference): Response
    {
        $user = $doctrine->getRepository(User::class)->find($id);
        if ($this->getUser() != $user) {
            return $this->createAccessDeniedException();
        }

        $order = $doctrine->getRepository(Order::class)->findOneBy(['reference' => $reference]); 

        if(!$order || $order->getUser() != $this->getUser()){
            return $this->redirectToRoute('app_profil', ['id'=> $this->getUser()->getId()]);
        }

        return $this->render('users/profil/order.html.twig', [
            'order' => $order,
        ]);
    }


    //=============================================
    //          Page Liste des abonnements
    //=============================================
    #[Route('/artistsfollowed', name: 'FollowedArtistsList')]
    public function indexFollowed(ManagerRegistry $doctrine): Response
    {

        //Récupération utilisateur connecté
        $connectedUser = $this->getUser();
        $idConnected = $connectedUser->getId();

        $follows = $doctrine->getRepository(Subscriptions::class)->findBy(['User' => $idConnected]);

        foreach ($follows as $artistsInst) {
            //Récupération id de l'artiste
            $artistId = $artistsInst->getId(); 

            //Récupération nombre d'abonnés de l'artiste
            $subscribers = $doctrine->getRepository(Subscriptions::class)->findBy(['UserFollowed' => $artistId]); //
            $subscribersCount = count($subscribers);
            $followerCountArray[$artistId] = $subscribersCount;

            //Récupération de si l'utilisateur connecté est abonné à cet artiste ou non
            $subscriptionsFound = $doctrine->getRepository(Subscriptions::class)->findBy(['UserFollowed' => $artistId, 'User' => $idConnected]);

            $subscribedCount = count($subscriptionsFound);
            if ($subscribedCount > 0){
                $IsFollowedArray[$artistId] = true;
            }
            else{
                $IsFollowedArray[$artistId] = false;
            }
            

        }



        return $this->render('users/profil/subscriptions.twig', [
            'artists' => $follows,
            'followers' => $followerCountArray,
            'isFollowed' => $IsFollowedArray
        ]);
    }


    //=============================================
    //          Page Liste des commentaires
    //=============================================
    #[Route('/mycomments', name: 'myComments')]
    public function indexComments(ManagerRegistry $doctrine): Response
    {

        //Récupération utilisateur connecté
        $connectedUser = $this->getUser();
        $idConnected = $connectedUser->getId();

        $comments = $doctrine->getRepository(Comments::class)->findBy(['User' => $idConnected]); 



        return $this->render('users/profil/comments.twig', [
            'comments' => $comments
        ]);
    }




}
