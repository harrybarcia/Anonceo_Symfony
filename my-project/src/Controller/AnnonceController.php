<?php

namespace App\Controller;

use App\Entity\Photo;
use App\Entity\Annonce;
use App\Form\AnnonceType;
use App\Repository\AnnonceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AnnonceController extends AbstractController
{
    #[Route('/gestion_annonce/afficher', name: 'annonce')]
    public function consulter_annonce(AnnonceRepository $repoannonce)
    {
        $annoncesArray = $repoannonce->findAll();
        return $this->render('annonce/consulter_annonce.html.twig',[
            "annonces"=>$annoncesArray
        ]);
    }

    #[Route('/gestion_annonce/ajouter', name: 'ajouter_annonce')]
    public function ajouter_annonce(Request $request, EntityManagerInterface $manager)
    {
        $annonce=new Annonce;
        // dd($annonce);
        $form = $this->createForm(AnnonceType::class, $annonce);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
        dump($request); 

            $manager->persist($annonce);
            $manager->flush();
                    // On récupère les images transmises
            $imageFile = $form->get('photo')->getData();
            if($imageFile)
            {
                for($c = 0; $c < count($imageFile); $c++)
                {
            
                // On génère un nouveau nom de fichier
                $nomImage = md5(uniqid()).'.'.$imageFile[$c]->guessExtension();
                
                // On copie le fichier dans le dossier uploads
                $imageFile[$c]->move(
                    $this->getParameter('photo_annonce'),
                    $nomImage
                );
                
                // On crée l'image dans la base de données
                $image = new Photo();
                $image->setNom($nomImage);
                $image->setAnnonce($annonce);
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
}
