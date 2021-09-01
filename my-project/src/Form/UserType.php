<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        if ($options['inscription']) {
            $builder

                ->add('email', TextType::class, ["required" => false])
                ->add('pseudo', TextType::class, ["required" => false])
                ->add('password', RepeatedType::class, [
                    'type' => PasswordType::class,
                    "first_name" => "first",
                    "required" => false,
                    "second_name" => "second",
                    "invalid_message" => "Les mots de passe ne sont pas identiques",
                    "first_options" => [
                        "label" => "Mot de passe"
                    ],
                    "second_options" => [
                        "label" => "Confirmation du mot de passe",
                    ], "constraints" => [new NotBlank([
                        "message" => "Veuillez saisir votre mdp"
                    ])]
                ])

                /*
                    RepeatedType permet de doubler un input
                    il faut définir leur "type"
                    leur donner des noms pour les différentier
                    first_name et second_name
                    en twig pour les afficher
                    ex formUser.password.first et formUser.password.second

                */

                // 1ere facon bien changer user et insc
                // ->add('password',TextType::class,["required"=>false,"label"=>"mot de passe"])
                // ->add('confirmPassword',TextType::class,["required"=>false,"label"=>"Confirmation du mot de passe"])
                ->add('nom', TextType::class, ["required" => false])
                ->add('prenom', TextType::class, ["required" => false, "label" => "Prénom"])
                ->add('adresse', TextType::class, ["required" => false, "label" => "adresse"])
                ->add('ville', TextType::class, ["required" => false, "label" => "ville"])
                ->add('cp', TextType::class, ["required" => false, "label" => "cp"])
                ->add('telephone', TextType::class, ["required" => false, "label" => "telephone"])
                ->add('civilite', TextType::class, ["required" => false, "label" => "civilite"])
                ->add('prenom', TextType::class, ["required" => false, "label" => "Prenom"])

            ;

        } elseif ($options['profil']) {
            $builder

                ->add('email', TextType::class, ["required" => false])
                ->add('nom', TextType::class, ["required" => false])
                ->add('prenom', TextType::class, ["required" => false, "label" => "Prénom"]);
        }
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'inscription' => false,
            'profil' => false
        ]);
    }
}
