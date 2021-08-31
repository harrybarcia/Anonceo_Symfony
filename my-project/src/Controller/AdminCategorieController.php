<?php

namespace App\Controller;

use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminCategorieController extends AbstractController
{
    #[Route('/admin/categorie', name: 'admin_categorie')]
    public function categorie_afficher(CategorieRepository $repoCategorie)
    {
        $categorieArray = $repoCategorie->findAll();
        //dd($categorieArray);
        return $this->render("admin_categorie/categorie_afficher.html.twig", [
            "categories" => $categorieArray
        ]);
        
    }
}
