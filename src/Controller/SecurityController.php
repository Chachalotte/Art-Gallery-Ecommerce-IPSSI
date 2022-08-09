<?php

// src/Controller/SecurityController.php
namespace App\Controller;

use App\Entity\User;
use App\Form\AdminType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SecurityController extends AbstractController
{
    /**
     * @Route("/logout", name="app_logout", methods={"GET"})
     */
    public function logout(): void
    {
        // controller can be blank: it will never be called!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }

    #[Route('/BecomAnAdmin/{id}', name: 'new_admin')]
    public function becomeAdmin(ManagerRegistry $doctrine, Request $request, $id): Response
    {
        $user= $doctrine->getRepository(User::class)->find($id);

        if(!$this->getUser() || ($this->getUser() != $user)){
            $this->addFlash("error", "Veuillez vous connecter");
            return $this->redirectToRoute('login');
        }

        $secret = $this->getParameter('secretAdminPw');

        $form = $this->createForm(AdminType::class);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get("secret")->getData() === $secret) {
                $user->setRoles(["ROLE_ADMIN"]);
                $em = $doctrine->getManager();
                $em->persist($user);
                $em->flush();

                $this->addFlash("success", "Nouvel admin enregistré");

                return $this->redirectToRoute("admin");
            } else {
                throw $this->createAccessDeniedException("Vous n'avez pas les droits admin");
            }
        }

        return $this->render("security/addAdmin.html.twig", [
            'form' => $form->createView(),
        ]);
    }
}
?>