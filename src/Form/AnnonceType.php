<?php

namespace App\Form;

use App\Entity\Annonce;
use App\Entity\City;
use App\Entity\Service;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnnonceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
            ])
            ->add('lieu_depart', EntityType::class, [
                'class' => City::class,
                'choice_label' => 'name',
            ])
            ->add('lieu_arrivee', EntityType::class, [
                'class' => City::class,
                'choice_label' => 'name',
            ])
            ->add('date', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('description')
            ->add('service', EntityType::class, [
                'class' => Service::class,
                'choice_label' => 'name',
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
