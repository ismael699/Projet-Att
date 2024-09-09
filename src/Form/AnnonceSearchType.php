<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\User;
use App\Entity\Annonce;
use App\Entity\Service;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AnnonceSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lieu_depart', EntityType::class, [
                'required' => false,
                'class' => City::class,
                'choice_label' => 'name',
                'attr' => ['class' => 'custom-select'], 
                'placeholder' => '.. Paris',
            ])
            ->add('lieu_arrivee', EntityType::class, [
                'required' => false,
                'class' => City::class,
                'choice_label' => 'name',
                'attr' => ['class' => 'custom-select'], 
                'placeholder' => '.. Lyon',
            ])
            ->add('date', DateType::class, [
                'required' => false,
                'input' => 'datetime', // utilise le format datetime pour la date
                'format' => 'yyyy-MM-dd', // format de la date
            ])
            ->add('service', EntityType::class, [
                'required' => false,
                'class' => Service::class,
                'choice_label' => 'name',
                'attr' => ['class' => 'custom-select'], 
            ])
            ->add('search', SubmitType::class, [
                'label' => 'Rechercher',
                'attr' => ['class' => 'btn-new'], 
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Annonce::class,
        ]);
    }
}
