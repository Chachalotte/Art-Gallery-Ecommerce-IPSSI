<?php

namespace App\Controller;

use App\Data\Mail;
use App\Entity\User;
use App\Entity\ResetPassword;
use App\Form\ResetPasswordType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ResetPasswordController extends AbstractController
{
    #[Route('/mot-de-passe-oublie', name: 'reset_password')]
    public function index(Request $request, ManagerRegistry $doctrine)
    {
        if($this->getUser()){
            return $this->redirectToRoute('home');
        }

        if($request->get('email')){
            $user = $doctrine->getRepository(User::class)->findOneBy(['email' => $request->get('email')]);
            if($user){
                $reset_password = new ResetPassword();
                $reset_password->setUser($user);
                $reset_password->setToken(uniqid());
                $reset_password->setCreatedAt(new \DateTime());
                $doctrine->getManager()->persist($reset_password);
                $doctrine->getManager()->flush();

                //envoi d'email mailjet
                $url = $this->generateUrl('update_password', [
                    'token' => $reset_password->getToken()
                ]);

                $content = "Bonjour ".$user->getFirstname().' '.$user->getName().",<br/>Vous avez demandé de réinitialiser votre mot de passe sur The art factory";
                $content .= "Merci de bien vouloir <a href='".$url."'>cliquer ici</a> pour mettre à jour votre mot de passe.";

                $mail = new Mail();                
                $mail->send($user->getEmail(), $user->getFirstname().' '.$user->getName(), 'Réinitialisation du mot de passe The art factory', $content);

                $this->addFlash('success', 'Envoie de l\'email pour la réinitialisation de votre mot de passe.');
            } else {
                $this->addFlash('error', 'Cette adresse email est inconnue.');
            }
        }

        return $this->render('reset_password/index.html.twig', [
            'controller_name' => 'ResetPasswordController',
        ]);
    }

    #[Route('/edit-mot-de-passe-oublie/{token}', name: 'update_password')]
    public function update($token, ManagerRegistry $doctrine, Request $request, UserPasswordHasherInterface $userPasswordHasher)
    {
        $reset_password = $doctrine->getRepository(ResetPassword::class)->findOneBy(['token' => $token]);

        if(!$reset_password){
            return $this->redirectToRoute('reset_password');
        }

        $now = new \DateTime();
        if($now > $reset_password->getCreatedAt()->modify('+ 3 hour')){
            $this->addFlash('success', 'Votre demande de mot de passe a expiré. Merci de la renouvellé');
            return $this->redirectToRoute('reset_password');
        } 

        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $newPw = $form->get('new_password')->getData();
            $password = $userPasswordHasher->hashPassword($reset_password->getUser(), $newPw);

            $reset_password->getUser()->setPassword($password);
            $doctrine->getManager()->flush();

            $this->addFlash('success', 'Votre mot de passe a bien été mis à jour.');
            return $this->redirectToRoute("login");
        }

        return $this->render('reset_password/update.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
