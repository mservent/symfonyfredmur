<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Discussion;
use App\Entity\Commentaire;
use App\Entity\Signalement;
use App\Entity\Utilisateur;
use App\Form\DiscussionType;
use App\Form\CommentaireType;
use App\Form\SignalementType;
use App\Entity\Discussionlike;
use App\Entity\DiscussionDislike;
use App\Repository\CategorieRepository;
use App\Repository\DiscussionRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CommentaireRepository;
use App\Repository\DiscussionlikeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\DiscussionDislikeRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class ForumController extends AbstractController
{
    /**
     * @Route("/forum", name="forum")
     */


    public function index(): Response
    {
        $repo = $this->getDoctrine()->getRepository(Discussion::class);

        //$discussions = $repo->findAll();
        $discussions = $repo->findBy([],['createdAt' => 'DESC']);

        return $this->render('forum/discussions/index.html.twig', [
            'controller_name' => 'ForumController',
            'discussions' => $discussions
        ]);
    }


/** liste des catégories avec des liens sur leurs discussions
 * @Route("/forum/navcategories", name="forum_navcategories")
 * 
 */

public function navCategorie() : Response
{
    $repo = $this->getDoctrine()->getRepository(Categorie::class);
    $categories = $repo->findBy([],['nom' => 'ASC']);

    return $this->render('forum/categories/categorie.html.twig', [
       'categories' => $categories
    ]);
}


/**
 * Affichage des discussions par catégories
 * 
 * 
 * @Route("/forum/categorieDiscussions/{id}", name="forum_categorie_discussions", methods={"GET","POST"}, requirements={"id"="\d+"})
 * @ParamConverter("categorie", options={"id" = "id"})
 * @param Categorie $id
 */

public function listeCategorieDiscussions(int $id = null, DiscussionRepository $repoDiscussions) : Response
{
     
   $discussions = $repoDiscussions-> findBy(
       ['categorie' => $id], ['createdAt' => 'DESC']);
    
   
    return $this->render('forum/categories/categorie_discussions.html.twig', [
        'discussions' => $discussions
     ]);
}


    /**
     * @Route("/", name="home") 
     */

    public function home(): Response //crée une page d'accueil
    {
        return $this->render('forum/home.html.twig'); 
    }



    /**
     * @Route("/forum/create", name="forum_create") 
     * @Route("/forum/{id}/edit", name="forum_edit")
     */

    public function formDiscussion(Discussion $discussion = null ,Request $request, EntityManagerInterface $entityManager) : Response
     {

        if(!$discussion){
        $discussion = new Discussion();
        }
        
        $user = $this->getUser();
        if(!$user){
            return $this->redirectToRoute('security_login');

        }

        $authorizedUser = $user->getIsValid();

        if(!$authorizedUser){
            return $this->redirectToRoute('forum_interdit'); 
        }
        
        $form = $this->createForm(DiscussionType::class, $discussion);
        
        $idUser = $user->getId();

        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid() && $user) {
            if(!$discussion->getId()){
                $discussion->setCreatedAt(new \DateTime());
                $discussion->setIsValid(true);
                $discussion->setUpDateAt(new \DateTime());
                $discussion->setUtilisateur($user);
        
            }
      

            $entityManager->persist($discussion);
            $entityManager->flush();

            return $this->redirectToRoute('forum_show', ['id' => $discussion->getId()]);
     
        }
       
        return $this->render('forum/discussions/create.html.twig', [
            'formDiscussion' => $form->createView(),
            'editMode' =>$discussion->getId() !== null,
            'authorizedUser' => $authorizedUser
        ]); 

    }


    /**
     * @Route("/forum/{id<[0-9]+>}", name="forum_show") 
     */
    public function show(int $id, Request $request, EntityManagerInterface $entityManager) : Response
    {
        $user = $this->getUser();
        if(!$user){
            return $this->redirectToRoute('security_login');  
        }

       

        $commentaire = new Commentaire();
        $form = $this->createForm(CommentaireType::class, $commentaire);

        $repo1 = $this->getDoctrine()->getRepository(Discussion::class);
        $discussion = $repo1->find($id);
        
        $form->handleRequest($request);

        $authorizedUser = $user->getIsValid();

       
        if($form->isSubmitted() && $form->isValid() && $user){
            if($authorizedUser){
            $commentaire->setCreatedAt(new \DateTime())
                        ->setUtilisateur($user)
                        ->setDiscussion($discussion);
            $entityManager->persist($commentaire);
            $entityManager->flush(); 
            }
        

            return $this->redirectToRoute('forum_show', ['id' => $discussion->getId()]);
        }
        
       
        $id_user = $discussion->getUtilisateur();

        $repo2 = $this->getDoctrine()->getRepository(Utilisateur::class);
        $utilisateur = $repo2->find($id_user);
        $auteur = $utilisateur->getNomUtilisateur();

        return $this->render('forum/discussions/show.html.twig', [
            'discussion' => $discussion,
            'auteur' => $auteur,
            'authorizedUser' => $authorizedUser,
            'commentaireForm' =>$form->createView()
         ]); 
             

    }  


    

/**
 * Permet de liker ou de disliker une discussion
 *
 * @Route("/forum/{id}/like", name= "discussion_like")
 * 
 * @param Discussion $discussion
 * @param EntityManagerInterface $entityManager
 * @param DiscussionlikeRepository $likeRepo
 * @return Response
 */
    public function like (Discussion $discussion, EntityManagerInterface $entityManager, DiscussionlikeRepository $likeRepo) : Response
    {
        $user = $this->getUser();
        if(!$user){
            return $this->redirectToRoute('security_login');  
        }
        $statutUser = $user->getIsValid();

        if(!$user) return $this->json([ 'code' => 403, 'message' => "Unauthorized"], 403);
        if(!$statutUser) return $this->json([ 'code' => 403, 'message' => "Non autorisé"], 403);

        if($discussion->isLikedByUser($user)) { //si l'utilisateur aime, on récupère notre like
            $like = $likeRepo->findOneBy([
                   'discussion' => $discussion,
                   'utilisateur' => $user
            ]);
           $entityManager->remove($like);
           $entityManager->flush();

           return $this->json([
               'code' => 200,
               'message' => 'Like bien supprimé',
               'likes' => $likeRepo->count(['discussion' => $discussion])
           ], 200);

        }

        if(!$discussion->isDisLikedByUser($user)){       
            $like = new Discussionlike();
            $like->setDiscussion($discussion)
                 ->setUtilisateur($user);

            $entityManager->persist($like);
            $entityManager->flush();

       return $this->json([
           'code' => 200, 
           'message' => 'Like bien ajouté',
           'likes' => $likeRepo->count(['discussion' => $discussion])
        ], 200);
    }
}


    
/**
 * Permet de disliker une discussion
 *
 * @Route("/forum/{id}/dislike", name= "discussion_dislike")
 * 
 * @param Discussion $discussion
 * @param EntityManagerInterface $entityManager
 * @param DiscussionDislikeRepository $likeRepo
 * @return Response
 */
public function dislike (Discussion $discussion, EntityManagerInterface $entityManager, DiscussionDislikeRepository $dislikeRepo) : Response
{
    $user = $this->getUser();
    $statutUser = $user->getIsValid();

    if(!$user) return $this->json([ 'code' => 403, 'message' => "Unauthorized"], 403);
    if(!$statutUser) return $this->json([ 'code' => 403, 'message' => "Non autorisé"], 403);

    if($discussion->isDisLikedByUser($user)) { //si l'utilisateur aime, on récupère notre dislike
        $dislike = $dislikeRepo->findOneBy([
               'discussion' => $discussion,
               'utilisateur' => $user
        ]);
       $entityManager->remove($dislike);
       $entityManager->flush();

       return $this->json([
           'code' => 200,
           'message' => 'Dislike bien supprimé',
           'likes' => $dislikeRepo->count(['discussion' => $discussion])
       ], 200);

    }

    if(!$discussion->isLikedByUser($user)){
        $dislike = new DiscussionDislike();
        $dislike->setDiscussion($discussion)
             ->setUtilisateur($user);

        $entityManager->persist($dislike);
        $entityManager->flush();

   return $this->json([
       'code' => 200, 
       'message' => 'Dislike bien ajouté',
       'dislikes' => $dislikeRepo->count(['discussion' => $discussion])
    ], 200);
}
}


/**
 * @Route("/forum/{id}/report/discussion", name="forum_report_discussion")
 */

public function reportDiscussion(int $id, Request $request, Discussion $discussion, DiscussionRepository $discussionRepo, EntityManagerInterface $entityManager) : Response
{
    $user = $this->getUser();
    $authorizedUser = $user->getIsValid();
    if(!$authorizedUser){
        return $this->redirectToRoute('forum_interdit');  
    }
    

    $discussion = $discussionRepo->find($id);
   

    $signalement = new Signalement();

    $form = $this->createForm(SignalementType::class, $signalement);

    $form->handleRequest($request);


    if($form->isSubmitted() && $form->isValid() && $user){
        $signalement->setCreatedAt(new \DateTime())
                    ->setUtilisateur($user)
                    ->setDiscussion($discussion);
                   

        $entityManager->persist($signalement);
        $entityManager->flush(); 
    
    

        return $this->redirectToRoute('forum');
    }

    return $this->render('forum/report/discussion.html.twig', [
        'discussion' => $discussion,
        'utilisateur' => $user,
        'formSignalement' => $form -> createView()  
     ]); 





}

/**
 * @Route("/forum/{id}/report/commentaire", name="forum_report_commentaire")
 */

public function reportCommemtaire(int $id, Request $request, CommentaireRepository $commentaireRepo, EntityManagerInterface $entityManager) : Response
{
    $user = $this->getUser();
    $authorizedUser = $user->getIsValid();
    if(!$authorizedUser){
        return $this->redirectToRoute('forum_interdit');  
    }

     $commentaire = $commentaireRepo->find($id);

    $signalement = new Signalement();

    $form = $this->createForm(SignalementType::class, $signalement);

    $form->handleRequest($request);


    if($form->isSubmitted() && $form->isValid() && $user){
        $signalement->setCreatedAt(new \DateTime())
                    ->setUtilisateur($user)
                    ->setCommentaire($commentaire);
                   

        $entityManager->persist($signalement);
        $entityManager->flush(); 
    
    

        return $this->redirectToRoute('forum');
    }


    return $this->render('forum/report/commentaire.html.twig', [
        'commentaire' => $commentaire,
        'utilisateur' => $user,
        'formSignalement' => $form -> createView()  
     ]); 





}


/**
 * Affiche la page de redirection pour un utilisateur bloqué
 *
 * @Route("/forum/interdit", name="forum_interdit") 
 */
    public function userInvalide(){
        return $this->render('forum/interdit.html.twig'); 
    }

}
