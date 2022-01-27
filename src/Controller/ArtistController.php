<?php

namespace App\Controller;

//Entitées
use App\Entity\Artist;

//Managers
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;


class ArtistController extends AbstractController
{
    //=============================================
    //          Page Liste des artistes
    //=============================================
    #[Route('/artist', name: 'artistList')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $artists = $doctrine->getRepository(Artist::class);

        $artistList = $artists->findAll();

     
        return $this->render('artists/artistList.html.twig', [
            'artist' => $artistList
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


}
