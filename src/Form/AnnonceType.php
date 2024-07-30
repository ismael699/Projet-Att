<?php

namespace App\Form;

use App\Entity\Annonce;
use App\Entity\City;
use App\Entity\Service;
use App\Entity\User;
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
                'required' => true, // rend le champ obligatoire
                'attr' => ['placeholder' => 'Entreprise ATT, ..'], // ajoute un attribut HTML pour le placeholder
            ])
            ->add('lieu_depart', EntityType::class, [
                'required' => true, // rend le champ obligatoire
                'class' => City::class,
                'choice_label' => 'name',
                'attr' => ['class' => 'custom-select'], // ajout de la class css
            ])
            ->add('lieu_arrivee', EntityType::class, [
                'required' => true, // rend le champ obligatoire
                'class' => City::class,
                'choice_label' => 'name',
                'attr' => ['class' => 'custom-select'], // ajout de la class css
            ])
            ->add('date', DateType::class, [
                'input' => 'datetime', // Utilise le format datetime pour la date
                'format' => 'yyyy-MM-dd', // Format de la date
                'html5' => true, // Utilise le type de champ HTML5
                'required' => true, // rend le champ obligatoire
                'attr' => ['placeholder' => 'Entreprise ATT, ..'], // ajoute un attribut HTML pour le placeholder
            ])
            ->add('description', TextareaType::class, [
                'required' => true, // rend le champ obligatoire
                'attr' => ['placeholder' => 'A propos, ..', 'class' => 'textarea'], // ajoute un attribut HTML pour le placeholder
            ])
            ->add('service', EntityType::class, [
                'required' => true, // rend le champ obligatoire
                'class' => Service::class,
                'choice_label' => 'name',
                'attr' => ['class' => 'custom-select'], // ajout de la class css
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Annonce::class,
        ]);
    }
}
