<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Discussion;
use App\Form\EditUserType;
use App\Entity\Commentaire;
use App\Entity\Signalement;
use App\Entity\Utilisateur;
use App\Form\CategorieType;
use App\Form\DiscussionType;
use App\Form\CommentaireType;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CommentaireRepository;
use App\Repository\SignalementRepository;
use App\Repository\UtilisateurRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


    /**
     * @Route("/admin", name="admin_")
     */

class AdminController extends AbstractController
{
 
 /**
  * @Route("/", name="index")
  */
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * Liste les utilisateurs du site
     * 
     * @Route("/utilisateurs", name="utilisateurs")
     */

    public function utilisateursListe(UtilisateurRepository $users){
        return $this->render("admin/utilisateurs.html.twig", [
            'users' => $users->findAll()
        ]);
    }

 /**
     * Liste les catégories du site
     * 
     * @Route("/categorie", name="categorie")
     */

    public function categorieListe(CategorieRepository $categories){
        return $this->render("admin/categorie.html.twig", [
            'categories' => $categories->findAll()
        ]);
    }



/**
     * @Route("/categorie/create", name="create_categorie") 
     * @Route("/categorie/modifier/{id}", name="modifier_categorie")
     */

    public function formCategorie(Categorie $categorie = null, Request $request, EntityManagerInterface $entityManager) : Response {
        if(!$categorie){
            $categorie = new Categorie();
            } 
    
            $form = $this->createForm(CategorieType::class, $categorie);
            $form->handleRequest($request);
    
            if($form->isSubmitted() && $form->isValid()){
                $entityManager->persist($categorie);
                $entityManager->flush();
        
                $this->addFlash('info', 'Catégorie modifiée avec succès');
                return $this->redirectToRoute('admin_categorie');
        
               }
        
               return $this->render('admin/createCat.html.twig', [
                   'formCategorie' => $form->createView(),
                   'editMode' =>$categorie->getId() !== null
               ]);
            }
    
    
 
/**
 * Modifier un utilisateur
 * 
 *
 * @Route("/utilisateur/modifier/{id}", name="modifier_utilisateur")
 */

public function editUtilisateur(Utilisateur $user, Request $request, EntityManagerInterface $entityManager) : Response
{
  $form = $this->createForm(EditUserType::class, $user);
  $form->handleRequest($request);

  if($form->isSubmitted() && $form->isValid()){
   $entityManager->persist($user);
   $entityManager->flush();

   $this->addFlash('info', 'Utilisateur modifié avec succès');
   return $this->redirectToRoute('admin_utilisateurs');

  }

  return $this->render('admin/editutilisateur.html.twig', [
      'userForm' => $form->createView()
  ]);
}


      /**
     * Liste les signalements qui n'ont pas encore été traités: propriété traitement =false
     * 
     * @Route("/signalements", name="signalements")
     */

    public function listeSignalement(SignalementRepository $signalements){
              return $this->render("admin/signalements.html.twig", [
            'signalements' => $signalements->findBy(['traitement'=>false],['createdAt' => 'ASC'])
        ]);
    }
    
    
    /**
 * traitement du commentaire signalé
 * 
 * 
 * @Route("/commentaire/traiter/{id}", name="traiter_commentaire")
 */

 public function traiteCommentaire (Commentaire $commentaire,  Request $request, EntityManagerInterface $entityManager) : Response
 {
      
    $form = $this->createForm(CommentaireType::class, $commentaire);
    $form->handleRequest($request);


    if($form->isSubmitted() && $form->isValid()) {
            
             $entityManager->persist($commentaire);
             $entityManager->flush();

             $this->addFlash('info', 'Commentaire modifié avec succès');

        return $this->redirectToRoute('admin_signalements');
 
    }

    return $this->render("admin/traiteComment.html.twig", [
        'formCommentaire' => $form->createView(),
        'commentaire' => $commentaire,
        
       
    ]);
 }

 
/**
 * @Route("/commentaire/supprimer/{id}", name="supprimer_commentaire", methods={"GET","POST"}, requirements={"id"="\d+"})
 * 
 */
public function supprimeCommentaire (int $id, Commentaire $commentaire, CommentaireRepository $repoCommentaire,   EntityManagerInterface $entityManager) : Response
{
   $commentaire = $repoCommentaire->find($id);
   $entityManager->remove($commentaire);
   $entityManager->flush();
   
   $this->addFlash('info', 'Commentaire supprimé avec succès');

   return $this->redirectToRoute('admin_signalements');
}

/**
 * traitement de la discussion signalée
 * 
 *
 * @Route("/discussion/traiter/{id}", name="traiter_discussion")
 */

public function traiteDiscussion (Discussion $discussion, Request $request, EntityManagerInterface $entityManager) : Response
{
    $form = $this->createForm(DiscussionType::class, $discussion);
    $form->handleRequest($request);


    if($form->isSubmitted() && $form->isValid()) {
             $discussion->setUpDateAt(new \DateTime());
             $entityManager->persist($discussion);
             $entityManager->flush();

             $this->addFlash('info', 'Discussion modifiée avec succès');

        return $this->redirectToRoute('admin_signalements');
 
    }
   
    return $this->render('admin/traiteDiscussion.html.twig', [
        'formDiscussion' => $form->createView()
       
    ]); 

}    
    
}
