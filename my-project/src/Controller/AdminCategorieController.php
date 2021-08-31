<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminCategorieController extends AbstractController
{
    // ajout categorie_afficher
    #[Route('/gestion_categorie/afficher', name: 'categorie_afficher')]
    public function categorie_afficher(CategorieRepository $repoCategorie)
    {
        
        $categorieArray = $repoCategorie->findAll();
        //dd($categorieArray);
        return $this->render("admin_categorie/categorie_afficher.html.twig", [
            "categories" => $categorieArray
        ]);
        
    }
// ajout categorie_ajouter_modifier
    #[Route("/gestion_categorie/ajouter", name:"categorie_ajouter")]
    #[Route("/gestion_categorie/modifier/{id}", name:"categorie_modifier")]
    public function categorie_ajouter_modifier(Categorie $categorie = null, Request $request, EntityManagerInterface $manager)
    {
        
        /*

            Le code pour ajouter et modifier est identique
            à part l'objet
            Quand on ajoute une catégorie on créé un nouvel objet 
            Et si on modifie une catégorie, autrement dit cette catégorie existe et se trouve dans la base de données

            On a 2 routes pour la même fonction
            on doit donc différentier l'objet soit on injecte par la modifier le paramètre id dans l'objet en dépendance
            soit on doit créer un nouvel objet (pour l'ajout)


        */
        if(!$categorie)
        {
            $categorie = new Categorie;
        }

        //dd($categorie);
        $form = $this->createForm(CategorieType::class, $categorie);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $modif = $categorie->getId() !== null;

            $manager->persist($categorie);
            $manager->flush();

            $this->addFlash("success", ($modif) ? "La catégorie N°" . $categorie->getId() . " a bien été modifiée" : "La catégorie N°" . $categorie->getId() . " a bien été ajoutée" );
            
            return $this->redirectToRoute('categorie_afficher');
        }

        return $this->render("admin_categorie/categorie_ajouter_modifier.html.twig", [
            "formCategorie" => $form->createView(),
            "categorie" => $categorie,
            "modification" => $categorie->getId() !== null
        ]);
    }
// ajout categorie_supprimer

#[Route("/gestion_categorie/supprimer/{id}", name:"categorie_supprimer")]

public function categorie_supprimer(Categorie $categorie, EntityManagerInterface $manager){


    $manager->remove($categorie);
    $manager->flush();

    $this->addFlash("success","la categorie a bien été supprimée"); 


    return $this->redirectToRoute("categorie_afficher");
}

}
