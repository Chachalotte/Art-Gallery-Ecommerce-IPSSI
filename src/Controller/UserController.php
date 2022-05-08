<?php

namespace App\Controller;

//Entitées
use App\Entity\User;
use App\Entity\Artist;

//Managers
use App\Entity\Comments;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class UserController extends AbstractController
{
    //=============================================
    //          Page de profil de l'utilisateur
    //=============================================
    #[Route('/profile', name: 'userProfile')]
    public function OwnProfile(ManagerRegistry $doctrine): Response
    {
        $artists = $doctrine->getRepository(User::class);
        $comments = $doctrine->getRepository(Comments::class);

        $user = $this->getUser();

        //$userID = $this->getId();

        // $comment = $user->getComments();

        //$artistList = $artists->findAll();

        //$userComments = $comments->findUserComments($userID);
        //$userEmail = $doctrine->getRepository(User::class)->find($user->getEmail());


        return $this->render('users/users.html.twig', [
            'user' => $user,
            // 'comments' => $comment
        ]);
    }


    //=============================================
    //          Page de profil d'un utilisateur
    //=============================================
    #[Route('/profile/{id}', name: 'userProfileID')]
    public function profileSelect(ManagerRegistry $doctrine, int $id, Request $request): Response
    {
        $users = $doctrine->getRepository(User::class);
        $comments = $doctrine->getRepository(Comments::class);

        $user = $doctrine->getRepository(User::class)->find($id);

        //$userID = $this->getId();

        $comment = $user->getComments();



        //$userComments = $comments->findUserComments($userID);
        //$userEmail = $doctrine->getRepository(User::class)->find($user->getEmail());


        return $this->render('users/users.html.twig', [
            'user' => $user,
            'comments' => $comment
        ]);
    }


    //=============================================
    //        Page d'un artiste (selon ID)
    //=============================================
    #[Route('/artist/{id}', name: 'artist')]
    public function ArtistSelect(ManagerRegistry $doctrine, int $id, Request $request): Response
    {

        //Lister un artiste spécifiquement par son id
        $artist = $doctrine->getRepository(Artist::class)->find($id);
        $URL = $request->getRequestUri();



        return $this->render('artists/artist.html.twig', [
            'controller_name' => 'ArtistController',
            'artist' => $artist,
            'URL' => $URL
        ]);
    }

    //=============================================
    //          Page Liste des artistes
    //=============================================
    #[Route('/artists', name: 'artistList')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $artists = $doctrine->getRepository(Artist::class);

        $artistList = $artists->findAll();


        return $this->render('artists/artistList.html.twig', [
            'artist' => $artistList
        ]);
    }
}
