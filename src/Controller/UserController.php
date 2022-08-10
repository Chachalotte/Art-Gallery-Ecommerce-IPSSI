<?php

namespace App\Controller;

//Entitées
use App\Entity\User;
use App\Entity\Product;
use App\Entity\Subscriptions;
//Managers
use App\Entity\Comments;
use App\Form\ProfilType;
use App\Form\ProductType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    // //=============================================
    // //          Edition page de profil de l'utilisateur
    // //=============================================
    // #[Route('/profil/edit/{id<\d+>}', name: 'app_profil_edit')]
    // public function update(ManagerRegistry $doctrine, Request $request, UserPasswordHasherInterface $userPasswordHasher, $id): Response
    // {
    //     // On sélectionne le bonne animal selon l'ID
    //     $user = $doctrine->getRepository(User::class)->find($id);
    //     if ($this->getUser() != $user) {
    //         return $this->createAccessDeniedException();
    //     }

    //     $email = new Email($user->getEmail());
    //     $user->setEmail($email);
    //     // $avatar = new File($this->getParameter('upload_file_user') . $user->getAvatar());
    //     // $user->setAvatar($avatar);
    //     // $avatar = new File($this->getParameter('upload_file_user') . $user->getAvatar());
    //     // $user->setAvatar($avatar);
    //     // $avatar = new File($this->getParameter('upload_file_user') . $user->getAvatar());
    //     // $user->setAvatar($avatar);
    //     // $avatar = new File($this->getParameter('upload_file_user') . $user->getAvatar());
    //     // $user->setAvatar($avatar);
    //     // $avatar = new File($this->getParameter('upload_file_user') . $user->getAvatar());
    //     // $user->setAvatar($avatar);
    //     // $avatar = new File($this->getParameter('upload_file_user') . $user->getAvatar());
    //     // $user->setAvatar($avatar);
    //     // $avatar = new File($this->getParameter('upload_file_user') . $user->getAvatar());
    //     // $user->setAvatar($avatar);

    //     $form = $this->createForm(ProfilType::class, $user);

    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         //         // if ($form->get('password')->getData() !== null) {
    //         //     $user->setPassword(
    //         //         $userPasswordHasher->hashPassword($user, $form['plainPassword']->getData())
    //         //     );
    //         // } else {
    //         //     $user->setPassword($oldUser->getPassword());
    //         // }



    //     return $this->render("users/profil.html.twig", [
    //         'form' => $form->createView(),
    //         'user' => $user
    //     ]);
    // }

    //=============================================
    //          Page Liste des artistes
    //=============================================
    #[Route('/artists', name: 'artistList')]
    public function index(ManagerRegistry $doctrine, Request $request, PaginatorInterface $paginator): Response
    {

        $artists = $doctrine->getRepository(User::class)->findAll();

        //Récupération utilisateur connecté
        $connectedUser = $this->getUser();
        
        if ($connectedUser){
            $idConnected = $connectedUser->getId();
        }
        else{
            $idConnected = 0;
        }

        $allArtistArray = array();

        foreach ($artists as $userInst) {
            //Récupération id de l'artiste
            $userId = $userInst->getId(); 
            $userRoles = $userInst->getRoles(); 

            if(in_array('ROLE_ARTIST', $userRoles))
            {
                $allArtistArray[$userId] = $userInst;
            }
        }
        $artistArray = $paginator->paginate(
            $allArtistArray,
            $request->query->getInt('page', 1),
            20
        );

        $followerCountArray = array();
        foreach ($artistArray as $artistsInst) {
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



        return $this->render('users/artist/artistList.html.twig', [
            'artists' => $artistArray,
            'followers' => $followerCountArray,
            'isFollowed' => $IsFollowedArray,
        ]);
    }

    //=============================================
    //          Page des artistes
    //=============================================
    #[Route('/artistsPage/{id<\d+>}', name: 'artistPage')]
    public function artistPage(ManagerRegistry $doctrine, $id)
    {
        $product = $doctrine->getRepository(Product::class)->findAll();
        $user = $doctrine->getRepository(User::class)->find($id); 
        $request = Request::createFromGlobals();

        $connectedUser = $this->getUser();
        if ($connectedUser){
            $idConnected = $connectedUser->getId();
        }
        else{
            $idConnected = 0;
        }

        $subscriptionsFound = $doctrine->getRepository(Subscriptions::class)->findBy(['UserFollowed' => $id, 'User' => $idConnected]);
        $subscribers = $doctrine->getRepository(Subscriptions::class)->findBy(['UserFollowed' => $id]);
        $subscribersCount = count($subscribers);

        $users = $doctrine->getRepository(User::class)->findAll();

        $artists = array();
        foreach ($users as $userArtist) {
            //Récupération id de l'artiste
            $userArtistId = $userArtist->getId(); 
            $userArtistRoles = $userArtist->getRoles(); 

            if(in_array('ROLE_ARTIST', $userArtistRoles))
            {
                $artists[$userArtistId] = $userArtist;
            }
        }
        shuffle($artists);
        $others = array_slice($artists, 0, 3);


        return $this->render('users/artist/artistPage.html.twig', [
            'user' => $user,
            'subscription' => $subscriptionsFound,
            'nbSubscribers' => $subscribersCount,
            'others' => $others
        ]);
    }


    //=============================================
    //          Page des artistes (> Action de s'abonner)
    //=============================================
    #[Route('/artistsPage/add/{id<\d+>}', name: 'follow')]
    public function add(Request $request, ManagerRegistry $doctrine, $id, User $user)
    {

        if (!$this->getUser()) {
            return $this->redirectToRoute('login');
        }
        else
        {
            $em = $doctrine->getManager();
            $user = $em->getRepository(User::class)->find($id);
    
            //$product = $doctrine->getRepository(Product::class)->findAll();
            $connectedUser = $this->getUser();
            //$request = Request::createFromGlobals();
    
            $idConnected = $connectedUser->getId();
            $subscriptionsFound = $doctrine->getRepository(Subscriptions::class)->findBy(['UserFollowed' => $id, 'User' => $idConnected], [],  1);


            $follow = new Subscriptions();
            $follow->setUser($connectedUser);
            $follow->setUserFollowed($user);
    

            if(!$subscriptionsFound || !$connectedUser){
                // On le persist et l'enregistre en BDD
                $em->persist($follow);
                $em->flush();
                $this->addFlash('success','Vous vous êtes abonné à cet artiste.');

            } else {
                $this->addFlash('error', "Vous n'avez pas les droits pour cette action");
            }

            $referer = $request->headers->get('referer');
            return $this->redirect($referer);
            //return $this->redirectToRoute('artistPage', ['id'=>$id]);


        }
        return $this->render('users/artist/artistPage.html.twig', [
            'user' => $user,
            'connectedUser' => $connectedUser
            // 'form' => $form->createView()
        ]);
    }


    //=============================================
    //          Page des artistes (> Action de se désabonner)
    //=============================================
    #[Route('/artistsPage/remove/{id<\d+>}', name: 'unfollow')]
    public function remove(Request $request, ManagerRegistry $doctrine, $id, User $user)
    {

        if (!$this->getUser()) {
            return $this->redirectToRoute('login');
        }
        else
        {
            $em = $doctrine->getManager();
            $user = $em->getRepository(User::class)->find($id);
    
            $connectedUser = $this->getUser();
            $idConnected = $connectedUser->getId();
    
            $subscriptionsFound = $doctrine->getRepository(Subscriptions::class)->findBy(['UserFollowed' => $id, 'User' => $idConnected], [],  1);
      

            if($subscriptionsFound && $connectedUser){
                foreach ($subscriptionsFound as $subscriptionsFoundInst) {
                    $em->remove($subscriptionsFoundInst);
                }
                //$em->remove($subscriptionsFound);
                $em->flush();
                $this->addFlash('success','Vous vous êtes désabonné de cet artiste.');

                $referer = $request->headers->get('referer');
                return $this->redirect($referer);

                //return $this->redirectToRoute('artistPage', ['id'=>$id]);
            } else {
                $this->addFlash('error', "Vous n'avez pas les droits pour cette action");
                return $this->redirectToRoute('home');
            }

        }
        return $this->render('users/artist/artistPage.html.twig', [
            'user' => $user,
            'connectedUser' => $connectedUser
            // 'form' => $form->createView()
        ]);
    }




}
