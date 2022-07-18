<?php

namespace App\Controller;

//Entitées
use App\Entity\User;
use App\Entity\Product;

//Managers
use App\Entity\Comments;
use App\Form\ProfilType;
use App\Form\ProductType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
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
    public function index(ManagerRegistry $doctrine): Response
    {
        // $artists = $doctrine->getRepository(User::class)->findBy(
        //     ['roles' => '["ROLE_ARTIST"]'],
        //     ['id' => 'DESC']
        // );
        $artists = $doctrine->getRepository(User::class)->findAll();

        // $artistList = $artists->findAllArtists();


        return $this->render('users/artist/artistList.html.twig', [
            'artists' => $artists
        ]);
    }

    //=============================================
    //          Page des artistes
    //=============================================
    #[Route('/artistsPage/{id<\d+>}', name: 'artistPage')]
    public function artistPage(ManagerRegistry $doctrine, $id)
    {
        $product = $doctrine->getRepository(Product::class)->findAll();
        $user = $doctrine->getRepository(User::class)->find($id); //tri par role
        $request = Request::createFromGlobals();

        return $this->render('users/artist/artistPage.html.twig', [
            'user' => $user
            // 'form' => $form->createView()
        ]);
    }
}
