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
                'label' => 'Lieu de départ',
                'class' => City::class,
                'choice_label' => 'name',
                'required' => false,
            ])
            ->add('lieu_arrivee', EntityType::class, [
                'label' => 'Lieu d\'arrivée',
                'class' => City::class,
                'choice_label' => 'name',
                'required' => false,
            ])
            ->add('date', DateType::class, [
                'label' => 'Date',
                'required' => false,
                'widget' => 'single_text',
            ])
            ->add('service', EntityType::class, [
                'class' => Service::class,
                'choice_label' => 'name',
                'required' => false,
            ])
            ->add('search', SubmitType::class, [
                'label' => 'Rechercher',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Annonce::class,
        ]);
    }
}
