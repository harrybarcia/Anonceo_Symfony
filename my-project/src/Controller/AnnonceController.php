<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Photo;
use App\Entity\Annonce;
use App\Form\AnnonceType;
use App\Entity\Commentaire;
use App\Repository\PhotoRepository;
use App\Repository\AnnonceRepository;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CommentaireRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use src\data\SearchData;
use src\data\SearchForm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AnnonceController extends AbstractController
{
    #[Route('/afficher', name: 'catalogue')]
    public function consulter_annonce(AnnonceRepository $repoannonce, CategorieRepository $repocategorie)
    {
        $annoncesArray = $repoannonce->findAll();
        $categoriesArray = $repocategorie->findAll();
        

        return $this->render('annonce/catalogue.html.twig',[
            "annonces"=>$annoncesArray,
            "categories"=>$categoriesArray,
            
        ]);
        
    }
/**
 * @Route("index", name="index")
 */
public function index(AnnonceRepository $repoannonce, Request $request): Response
{
    $data=new SearchData(); // je créé un objet et ses propriétés (q et categorie) et je le stocke dans $data
    $form=$this->createForm(SearchForm::class, $data);  // je créé mon formulaire qui utilise la classe searchForm que je viens de créé, je précise en second paramètre les données. Comme ça quand je vais faire un handle request ca va modifier cet objet (new search data) qui représente mes données

    $form->handleRequest($request);
    

    $annonces=$repoannonce->rechercher($data);
    // $filtre = $_GET["categorie"];
    // dump($filtre);
    // $test=$repoannonce->findByCategorie(["categorie"=>$filtre]);
    // if ($test) {
    //     return $this->render('annonce/test.html.twig', ["test"=>$test]);
    // }
    return $this->render('annonce/index.html.twig',[
        "filtre"=>$annonces,
        "form"=>$form->createView()
    ]); 
    
}

    /**
     * 
     * @Route("/afficher fiche_annonce/{id<\d+>}", name="fiche_annonce")
     */
    public function fiche_annonce(Annonce $annonceObject, AnnonceRepository $repoannonce, CommentaireRepository $repocommentaire)
                // $id, annonceRepository $repoannonce    
    {
        
    //     $test=$annonceObject->getCommentaires();
    //     if ($test) {
    //         for ($i=0; $i < count($test); $i++) { 
    //             dump($annonceObject->getCommentaires()[$i]->getCommentaire());
    //         }
    //     }
    dump($annonceObject);//Objet renvoie id, titre, desc_l, desc_c, user, categorie, photos, commentaires
    dump(gettype($annonceObject));//Objet
    dump($annonceObject->getPrix());//100
    dump(gettype($annonceObject->getPrix()));//integer
    dump($annonceObject->getCommentaires());//Objet
    dump(gettype($annonceObject->getCommentaires()));//Doctrine\ORM\PersistentCollection {#750 ▼
        dump(gettype($annonceObject->getCommentaires()));
        dump($repoannonce->findById(35)); // renvoie id, titre, desc_l, desc_c, user, categorie, photos, commentaires
        
        dump(gettype($repoannonce->findById($annonceObject->getId())));//Array
        dump($annonceObject->getId());// renvoie 36
        dump($repocommentaire->findAll());

        dump($annonceObject->getCommentaires());//Objet repo illisible
        dump(gettype($annonceObject->getCommentaires()));//Objet 
        dump($annonceObject->getCommentaires()[0]);//objet commentaire avec l'id, le commentaire en texte, la date_enr etc
        dump(gettype($annonceObject->getCommentaires()[0]));//objet
        $mesannonces=($annonceObject->getId());
        dump($annonceObject->getUser()->getId());
       dump($repocommentaire->findBy(["annonce"=>$mesannonces]));

        dump($utilisateur=$this->getUser());

        return $this->render("annonce/fiche_annonce.html.twig", [
            "annonce"=>$annonceObject,
            "commentaires"=>$repocommentaire->findBy(["annonce"=>$mesannonces]),
                    ]);
    }

    #[Route('/gestion_annonce/ajouter', name: 'ajouter_annonce')]
    
    public function ajouter_annonce(Request $request, EntityManagerInterface $manager)
    {

        if($this->isGranted('IS_ANONYMOUS')) //si la personne connectée est anonyme
        { 
            $this->addFlash(
            'success',
            'Veuillez vous connecter pour pouvoir déposer une annonce'
            );
                return $this->redirectToRoute("connexion");
        }
        // ----------Je créé un nouvel objet annonce------------
        $annonce=new Annonce;
        // dd($annonce);
        $form = $this->createForm(AnnonceType::class, $annonce, ['ajouter'=>true]);
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
            $photoFile = $form->get('photo')->getData();
            if($photoFile)
            {
                //-- le champs photo est un tableau de mon entité annonce--
                for($c = 0; $c < count($photoFile); $c++)
                {
            
                // --- pour chaque tour de boucle, je génère un nom-----
                $nomImage = md5(uniqid()).'.'.$photoFile[$c]->guessExtension();
                
                // --Je copie le fichier dans le dossier uploads--
                $photoFile[$c]->move(
                    $this->getParameter('photo_annonce'),
                    $nomImage
                    
                );
                // dd($photoFile);
                
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
            return $this->redirectToRoute("mes_annonces");

        }

        return $this->render('annonce/ajouter_annonce.html.twig',[
            "formAnnonce"=>$form->createView()
        ]);
    }

    #[Route('/profil/afficher', name: 'mes_annonces')]
    public function mesannonces(AnnonceRepository $repoannonce)
    {
        
            return $this->render('user/mes_annonces.html.twig',[
                "annonces"=>$repoannonce->findBy(['user'=>$this->getUser()->getId()]),
                

                ]);
        
    }
    #[Route('/admin/afficher', name: 'admin_annonces')]
    public function admin_annonce(AnnonceRepository $repoannonce)
    {

            return $this->render('admin/admin_consulter_annonces.html.twig',["annonces"=>$repoannonce->findAll()]);
        
    }

    #[Route("/profil/supprimer/{id}", name:"annonce_supprimer")]

    public function annonce_supprimer(Annonce $annonce, EntityManagerInterface $manager, PhotoRepository $repophotos, CommentaireRepository $repocommentaire)
    {
        $photos=$repophotos->findBy(["annonce"=>$annonce->getId()]);
        // dump($photos[0]->getNom());
        dump($annonce);
            if ($photos){
                for ($i=0; $i < count($photos) ; $i++) { 
                    unlink($this->getParameter("photo_annonce") . '/' . $photos[$i]->getNom()); 
                    $manager->remove($photos[$i]);        
                    }
                }
        $commentaire=$repocommentaire->findBy(["annonce"=>$annonce->getId()]);        
            if ($commentaire){
                for ($i=0; $i < count($commentaire) ; $i++) { 
                $manager->remove($commentaire[$i]);        
                    }
                }
        $manager->remove($annonce);
        $manager->flush();
        $this->addFlash(
           'success',
           "L'annonce a bien été supprimée")
        ;
        return $this->redirectToRoute("mes_annonces");
    }
    #[Route("/profil/modifier/{id}", name:"annonce_modifier")]

    public function annonce_modifier(Annonce $annonce, EntityManagerInterface $manager, Request $request)
    {

        $form = $this->createForm(AnnonceType::class, $annonce, array("modifier"=>true));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {

        $photoFile=$form->get('photoFile')->getData();

        if($photoFile){

            $nomImage = date("YmdHis") . "-" . uniqid() . "-" . $photoFile->getClientOriginalName();

            $photoFile->move(
                $this->getParameter("annonce_photo"), // dans services yaml, on upload dans image upload
                $nomImage
            );

            //dump($annonce->getPhotos());

            if($annonce->getPhotos())
            {

                /*
                    fonction php unlink() permet de supprimer un fichier
                    1 argument : emplacement suivi du nom du fichier
                */
                unlink($this->getParameter("photo_annonce") . '/' . $annonce->getPhotos());
            }
            $annonce->setNom($nomImage); // on redéfinit la propriété image qui est le nom de l'image

        }

        $manager->persist($annonce); //avec persist on peut ajouter ou modifier un annonce. Si l'id est null, il va créer le annonce si l'id
        // existe, il va l'update.
        $manager->flush(); 

        $this->addFlash("success", "Le annonce N°" . $annonce->getId() . " a bien été modifié");

        return $this->redirectToRoute("mes_annonces");
 
        }

        return $this->render('annonce/modifier_annonce.html.twig', [
            "annonce" => $annonce, /* ce 2eme argument est utile si on veut afficher des données de la variable dans le twig */
        "formAnnonce_modif"=>$form->createView()]);
    

    }
}
