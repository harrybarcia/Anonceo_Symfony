<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Photo;
use App\Entity\Annonce;
use App\Form\AnnonceType;
use App\Repository\PhotoRepository;
use App\Repository\AnnonceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AnnonceController extends AbstractController
{
    #[Route('/afficher', name: 'annonce')]
    public function consulter_annonce(AnnonceRepository $repoannonce, PhotoRepository $repophotos)
    {
        $annoncesArray = $repoannonce->findAll();

        return $this->render('annonce/consulter_annonce.html.twig',[
            "annonces"=>$annoncesArray,
            
        ]);
        
    }



    #[Route('/gestion_annonce/ajouter', name: 'ajouter_annonce')]
    
    public function ajouter_annonce(Request $request, EntityManagerInterface $manager)
    {
        
        // ----------Je créé un nouvel objet annonce------------
        $annonce=new Annonce;
        // dd($annonce);
        $form = $this->createForm(AnnonceType::class, $annonce);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
        // dump($request); 
            // ---------j'envoie l'objet en bdd-----------------
            $annonce->setDateEnr(new \DateTimeImmutable('now'));
            $user=$this->getUser();
            $annonce->setUSer($user);

            $manager->persist($annonce);
            $manager->flush();
            // --je vérifie si la pté photo contient de la data (name, type etc----
            $imageFile = $form->get('photo')->getData();
            if($imageFile)
            {
                //-- le champs photo est un tableau de mon entité annonce--
                for($c = 0; $c < count($imageFile); $c++)
                {
            
                // --- pour chaque tour de boucle, je génère un nom-----
                $nomImage = md5(uniqid()).'.'.$imageFile[$c]->guessExtension();
                
                // --Je copie le fichier dans le dossier uploads--
                $imageFile[$c]->move(
                    $this->getParameter('photo_annonce'),
                    $nomImage
                    
                );
                // dd($imageFile);
                
                // -- je créé un objet et je l'insère dans ma bdd
                $image = new Photo();
                $image->setNom($nomImage);
                $image->setAnnonce($annonce);// dans le champs annonce de mon objet image
                // il sait qu'il doit insérrer la clef primaire
                
                $manager->persist($image); // on persiste l'instance
                $manager->flush(); // on envoie l'instance en BDD
            
                }
            }    

            $this->addFlash("success", "L'annonce N°" . $annonce->getId() . " a bien été déposée");
            return $this->redirectToRoute("annonce");

        }

        return $this->render('annonce/ajouter_annonce.html.twig',[
            "formAnnonce"=>$form->createView()
        ]);
    }

    #[Route('/profil/afficher', name: 'mes_annonces')]
    public function mesannonces(AnnonceRepository $repoannonce)
    {
        $user=$this->getUser()->getId();
        $annoncesArray = $repoannonce->findBy(['user'=>$user]);//tableau des objets annonce
        
        
            return $this->render('user/mes_annonces.html.twig',[
                "annonces"=>$annoncesArray
                
            ]);
        
    }

    #[Route("/profil/supprimer/{id}", name:"annonce_supprimer")]

    public function annonce_supprimer(Annonce $annonce, EntityManagerInterface $manager, PhotoRepository $repophotos){
    

        
        $photos=$repophotos->findBy(['annonce'=>$annonce->getId()]);
        
        
        if($photos !== null){
            
            for($i = 0; $i < count($photos); $i++)
            {
                unlink($this->getParameter("photo_annonce") . '/' . $photos[$i]->getNom());
                $manager->remove($photos[$i]);
            }
        }

            //$photos peut etre un tableau vide ou avec des objets photo.
            // if photos: boucler dans le tableau les objets. count photo i++. 
                // unlink toutes les photos
                // remove chaque objet
            
        $manager->remove($annonce);
        $manager->flush();
    
        $this->addFlash("success","l'annonce a bien été supprimée"); 
    
    
        return $this->redirectToRoute("mes_annonces");
    }
}
