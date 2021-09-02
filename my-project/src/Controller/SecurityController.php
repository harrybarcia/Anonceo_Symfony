<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use DateTimeImmutable;
use App\Service\PasswordUpdate;
use Doctrine\ORM\EntityManager;
use App\Form\PasswordUpdateType;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecurityController extends AbstractController
{
    #[Route('/inscription', name: 'inscription')]
    public function inscription(Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $encoder)
    {
        $user=new User;
        $form=$this->createForm(UserType::class,$user,['inscription'=>true]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {

        $hash=$encoder->hashPassword($user, $user->getPassword());
        dump($hash);

            $user->setDateEnr(new \DateTimeImmutable('now'));
            $user->setPassword($hash);
            $manager->persist($user);
            $manager->flush();

            $this->addFlash("success", "Votre inscription a bien été enregistrée, vous pouvez vous connecter");

            return $this->redirectToRoute("connexion");
        }
        return $this->render('security/inscription.html.twig',[
            "formUser"=>$form->createView()
        ]);
    }

    #[Route('/connexion', name: 'connexion')]
    public function connexion(){
        return $this->render("security/connexion.html.twig");
    }

    #[Route('/deconnexion', name: 'deconnexion')]
    public function deconnexion(){}

    // lorsqu'un utilisateur s'authentifie, il est redirigé sur la route role qui permet de checker son role.
    #[Route('/roles', name: 'roles')]
    public function roles(){
        if($this->isGranted('ROLE_ADMIN')) //si la personne connectée est admin
        {
            return $this->redirectToRoute("back_office");
        }
        elseif($this->isGranted('ROLE_USER')) //si la personne connectée est admin
        {
            return $this->redirectToRoute("profil");
        }

    }
    
    /**
     * @Route("/profil", name="profil")
     */
    public function profil()
    {
        // La méthode getUser() permet de récupérer l'objet user provenant de la table User de l'utilisateur connecté
        
        $user = $this->getUser();
        //dd($user);

        return $this->render('user/profil.html.twig');
    }


    /**
     * @Route("/profil/modification", name="profil_modification")
     */
    public function profil_modification(Request $request, EntityManagerInterface $manager)
    {
        $user = $this->getUser();
        //dump($user);

        //$user->confirmPassword = $user->getPassword();
        //dd($user);

        $form = $this->createForm(UserType::class, $user, ["profil" => true]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {

            $manager->persist($user);
            $manager->flush();

            $this->addFlash("success", "Les données de votre profil ont bien été modifiées");
            return $this->redirectToRoute("profil");

        }


        return $this->render("user/profil_modification.html.twig", [
            "formUser" => $form->createView()
        ]);
    }

    /**
     * @Route("/profil/mot_de_passe/modification", name="password_modification" )
     */
    public function password_modification(Request $request, UserPasswordHasherInterface $encoder, EntityManagerInterface $manager)
    {

        $user = $this->getUser(); // objet de l'utilisateur connecté
        $passwordUpdate = new PasswordUpdate;
        // dd($passwordUpdate); montre 3 propriétés vides;   -oldPassword: null
        //   -newPassword: null
        //   -confirmPassword: null

        $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            

            //dd($passwordUpdate);
            /*
                1e étape : comparer oldPassword avec le mot de passe en BDD
            */

            // si l'ancien mot de passe (du formulaire) n'est pas égal à celui encodé en bdd
            // on rentre dans la condition qui créée un message d'erreur  
            if(!$encoder->isPasswordValid($user, $passwordUpdate->getOldPassword())) // boolean
            {
                $form->get('oldPassword')->addError(new FormError("L'ancien mot de passe est incorrect"));
            }
            else // oldPassword == $user->getPassword() ==> traitement des données, encoder newPassword puis l'injecter dans l'objet $user avant de persist et flush
            {
                $hash = $encoder->hashPassword($user, $passwordUpdate->getNewPassword());
                //dd($hash);
                
                $user->setPassword($hash);
                //dd($user->getPassword());
                $manager->persist($user);
                $manager->flush();

                $this->addFlash(
                   'success',
                   'Votre mot de passe a bien été modifié'
                );

                return $this->redirectToRoute('profil');


            }


            

        }
        else 
        {

            $this->addFlash(
               'error',
               'veuillez remplir le formulaire'
            );
        }

        return $this->render("user/password_modification.html.twig", [
            "formPassword" => $form->createView()
        ]);
    }



}