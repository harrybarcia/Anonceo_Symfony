<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Entity\Commentaire;
use App\Form\CommentaireType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CommentaireRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommentaireController extends AbstractController
{
    #[Route('/ajout_commentaire/{id}', name: 'ajout_commentaire')]
    public function deposer__commentaire(Request $request , EntityManagerInterface $manager, Annonce $annonce)
    {
        if($this->isGranted('IS_ANONYMOUS')) //si la personne connectée est anonyme
        { 
            $this->addFlash(
            'success',
            'Veuillez vous connecter pour pouvoir déposer un commentaire'
            );
                return $this->redirectToRoute("connexion");
        }
        $comment = new Commentaire;
        $form = $this->createForm(CommentaireType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) 
        {    
                $comment->setDateEnr(new \DateTimeImmutable('now'));
                $user=$this->getUser();
                dump($user);
                $comment->setUSer($user);
                ;
                $comment->setAnnonce($annonce);
                $manager->persist($comment);
                $manager->flush();
                $this->addFlash(
                   'success',
                   'Votre commentaire a bien été pris en compte'
                );
                return $this->redirectToRoute('mes_annonces');
        }
        return $this->render('commentaire/commentaire.html.twig',[
            "formComment"=>$form->createView()
        ]);
        
    }
    #[Route('/lire_commentaire/{id}', name: 'lire')]
    public function consulter_annonce(CommentaireRepository $repocommentaire)
    {
        $commentairesArray = $repocommentaire->findAll();

        return $this->render('commentaire/catalogue.html.twig',[
            "commentaires"=>$commentairesArray,
            
        ]);
        
    }

}
