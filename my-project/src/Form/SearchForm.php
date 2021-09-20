<?php

namespace src\data;

use App\Entity\Categorie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchForm extends AbstractType {
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('q', TextType::class, [
            'label'=>false,
            'required'=>false,
            'attr'=> ['placeholder'=> 'rechercher']
        ])
        ->add('categorie', EntityType::class, [
            'label'=>false,
            'required'=>false,
            'class'=>Categorie::class,
            'expanded'=>'true',
            'multiple'=>true
        ]);
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
        'data_class',
        'method'=>'GET',
        'csrf_protection'=>false
        ]);
    }

    
    public function getBlockPrefix()
    {
     return '';   
    }
}