<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Annonce;
use App\Entity\Categorie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;

class AnnonceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('titre', TextType::class, [
            "label" => "Titre du produit",
            "required" => false,
            "attr" => [
                "placeholder" => "Saisir le titre du produit",
                "class" => "bg-warning",
            ]
        ])
        ->add('desc_c', TextType::class, [
            "label" => "Description du produit",
            "required" => false,
            "attr" => [
                "placeholder" => "",
                "class" => "bg-warning",
            ]
        ])
        ->add('desc_l', TextType::class, [
            "label" => "Description longue du produit",
            "required" => false,
            "attr" => [
                "placeholder" => "",
                "class" => "bg-warning",
            ]
        ])
        ->add('prix', MoneyType::class, [
            "currency"=>"EUR",
            "required" => false,
            "attr" => [
                "placeholder" => "Saisir le prix du produit",
                "class" => "bg-warning",
            ]
        ])
        ->add('desc_c', TextType::class, [
            "label" => "Description du produit",
            "required" => false,
            "attr" => [
                "placeholder" => "Saisir la description du produit",
                "class" => "bg-warning",
            ]
        ])
        ->add('adresse', TextType::class, [
            "label"=>"adresse",
            "required" => false,
            "attr" => [
                "placeholder" => "Saisissez votre adresse",
                "class" => "bg-warning",
            ]
        ])
        ->add('cp', TextType::class, [
            "label"=>"CP",
            "required" => false,
            "attr" => [
                "placeholder" => "Saisir le CP",
                "class" => "bg-warning",
            ]
        ])
        ->add('ville', TextType::class, [
            "label" => "Ville",
            "required" => false,
            "attr" => [
                "placeholder" => "Saisir la description du produit",
                "class" => "bg-warning",
            ]
        ])
        ->add('user', EntityType::class, [ // cet input a une relation avec une autre entity
            "class" => User::class,        // avec quelle entity
            "choice_label" => "pseudo",
            "required" => false,          // quelle propriété (quel champ) afficher
            
        ])
        ->add('categorie', EntityType::class, [ // cet input a une relation avec une autre entity
            "class" => Categorie::class,        // avec quelle entity
            "choice_label" => "titre",          // quelle propriété (quel champ) afficher
            "placeholder" => "Saisir une catégorie"
        ])
            ->add('date_enr', DateType::class, [
                'widget' => 'choice',
                'input'  => 'datetime_immutable'
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Annonce::class,
        ]);
    }
}
