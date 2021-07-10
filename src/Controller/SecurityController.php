<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class SecurityController extends AbstractController
{

    /**
     * @Route("/inscription", name="security_registration")
     */
   
    public function registration(Request $request, EntityManagerInterface $entityManager,  UserPasswordHasherInterface $encoder) : Response
    {
        $utilisateur = new Utilisateur();

       $form = $this->createForm(RegistrationType::class, $utilisateur);

       $form->handleRequest($request);

       if($form->isSubmitted() && $form->isValid()) {
           $hash = $encoder->hashPassword($utilisateur, $utilisateur->getPassword());
            $isValid = true;
            $utilisateur->setPassword($hash);
            $utilisateur->setRoles( array ('ROLE_USER'));
            $utilisateur->setIsValid($isValid);
           $entityManager->persist($utilisateur);
           $entityManager->flush();

           return $this->redirectToRoute('security_login');
       }

       return $this->render('security/registration.html.twig', [
           'form' => $form->createView()
       ]);
   }


   /**
    * @Route("/connexion", name="security_login")
    */
   public function login(){
            return $this->render('security/login.html.twig');  
   }


   /**
    * @Route("/deconnexion", name="security_logout")
    */
    public function logout(){}
}
