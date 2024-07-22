<?php

namespace App\Form;

use App\Entity\City;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => false, // désactive l'affichage du label
                'required' => true, // rend le champ obligatoire
                'attr' => ['placeholder' => 'Nom'], // ajoute un attribut HTML pour le placeholder
            ])
            ->add('code', NumberType::class, [
                'label' => false, // désactive l'affichage du label
                'required' => true, // rend le champ obligatoire
                'attr' => ['placeholder' => 'Code postale'], // ajoute un attribut HTML pour le placeholder
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => City::class,
        ]);
    }
}
