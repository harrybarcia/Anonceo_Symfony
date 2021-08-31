<?php

namespace App\Controller;

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
            $manager->persist($annonce); // on persiste ce qu'on souhaite envoyer en BDD : l'objet $produit
            // on ne définit pas dans quelle table, car on envoit un objet issu d'une class (= Entity)
            // persist() => INSERT INTO / UPDATE
            $manager->flush(); // envoie en BDD
            dump($annonce);
            $this->addFlash("success", "L'annonce N°" . $annonce->getId() . " a bien été déposée");
            return $this->redirectToRoute("annonce");

        }

        return $this->render('annonce/ajouter_annonce.html.twig',[
            "formAnnonce"=>$form->createView()
        ]);
    }
}
