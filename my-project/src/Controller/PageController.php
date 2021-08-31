<?php

namespace App\Controller; // App = src

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PageController extends AbstractController // héritage de la class AbstractController
{

    /*
        En créant un controller, est généré également un dossier template du même nom



        Une route est "page web" : exemple : inscription.php, contact.php
        en local : localhost:8000
        en ligne : www.nomDeDomaine.fr

        Un controller peut contenir plusieurs routes


        Une annotation peut être écrite en commentaire mais elle est "lue" parce qu'elle commence par un @
        Une route contient 2 arguments :
        1e : la route (url) exemple : localhost:8000/page
        2e : le nom de la route : lien (href avec la fonction twig path() )

        Les arguments des annotations sont entre DOUBLE QUOTE


        #[Route('/page', name: 'page')]

        on peut également paramétrer les routes dans le fichier :
        config/routes.yaml

    */



    /**
     * la fonction page() permet 
     * 
     * @Route("/page", name="pageName")
     */


    // la méthode render() (qui provient de AbstractController) permet de relier une fonction(route) à une view (affichage sur le navigateur)
    // 2 arguments :
    // 1e (obligatoire) : fichier html.twig (qui se trouve dans le dossier templates)
    // 2e (facultatif) : tableau qui permet de véhiculer des valeurs du controller au twig

    // Comment accède t-on à une fonction ? par la route 



    /**
     * La fonction accueil() permet d'accéder à la page principale du site
     * 
     * @Route("/", name="accueil")
     */
    public function accueil()
    {
        return $this->render("Page/accueil.html.twig");
    }
    

    











}// fermeture de la class PageController
