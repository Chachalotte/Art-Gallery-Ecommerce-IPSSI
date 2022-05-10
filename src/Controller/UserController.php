<?php

namespace App\Controller;

//Entitées
use App\Entity\User;
use App\Entity\Artist;

//Managers
use App\Entity\Comments;
use App\Form\ProfilType;
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
    //          Page de profil de l'utilisateur
    //=============================================
    #[Route('/profil/{id<\d+>}', name: 'app_profil')]
    public function Profil(ManagerRegistry $doctrine,  Request $request, UserPasswordHasherInterface $userPasswordHasher, $id)
    {
        $user = $doctrine->getRepository(User::class)->find($id);
        if ($this->getUser() != $user) {
            return $this->createAccessDeniedException();
        }

        $comment = $user->getComments();

        // On récupère l'ancienne donnée
        $oldUser = new User;
        $oldUser->setEmail($user->getEmail());
        $oldUser->setFirstname($user->getFirstname());
        $oldUser->setName($user->getName());
        $user->getGender() ? $oldUser->setGender($user->getGender()) : null;
        $user->getAge() ? $oldUser->setAge($user->getAge()) : null;
        $oldUser->setPassword($user->getPassword());
        $user->getAvatar() ? $oldUser->setAvatar($user->getAvatar()) : null;

        // $oldUser->setDescription($user->getDescription());

        // $email = new Email($user->getEmail());
        // $user->setEmail($email);

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
                $user->setAvatar($oldUser->getAvatar());
            }

            $em = $doctrine->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash("success", "Le profil a bien été modifié");

            return $this->redirectToRoute("app_profil", ['id' => $user->getId()]);
        }

        return $this->render('users/profil.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'comments' => $comment
        ]);
    }

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
