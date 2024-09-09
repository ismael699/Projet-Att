<?php

namespace App\Form;

use App\Entity\Annonce;
use App\Entity\City;
use App\Entity\Service;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnnonceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'required' => true, 
                'attr' => ['placeholder' => 'Entreprise ATT, ..'], 
            ])
            ->add('lieu_depart', EntityType::class, [
                'required' => true, 
                'class' => City::class,
                'choice_label' => 'name',
                'attr' => ['class' => 'custom-select'], // ajout de la class css
            ])
            ->add('lieu_arrivee', EntityType::class, [
                'required' => true, // rend le champ obligatoire
                'class' => City::class,
                'choice_label' => 'name',
                'attr' => ['class' => 'custom-select'], 
            ])
            ->add('date', DateType::class, [
                'input' => 'datetime', // Utilise le format datetime pour la date
                'format' => 'yyyy-MM-dd', // Format de la date
                'html5' => true, // Utilise le type de champ HTML5
                'required' => true, 
                'attr' => ['placeholder' => 'Entreprise ATT, ..'], 
            ])
            ->add('description', TextareaType::class, [
                'required' => true, 
                'attr' => ['placeholder' => 'A propos, ..', 'class' => 'textarea'], 
            ])
            ->add('service', EntityType::class, [
                'required' => true, 
                'class' => Service::class,
                'choice_label' => 'name',
                'attr' => ['class' => 'custom-select'], 
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Annonce::class,
        ]);
    }
}
