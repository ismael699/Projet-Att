<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class EditUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('firstName', TextType::class, [
            'required' => true, // rend le champ obligatoire
            'attr' => ['placeholder' => 'John'], // ajoute un attribut HTML pour le placeholder
        ])
        ->add('lastName', TextType::class, [
            'required' => true, // rend le champ obligatoire
            'attr' => ['placeholder' => 'Doe'], // ajoute un attribut HTML pour le placeholder
        ])
        ->add('email', TextType::class, [
            'required' => true, // rend le champ obligatoire
            'attr' => ['placeholder' => 'email@gmail.com'], // ajoute un attribut HTML pour le placeholder
            'constraints' => [ // ajoute des contraintes de validation pour ce champ
                new NotBlank(['message' => 'Veuillez entrer une adresse email.']),
                new Email(['message' => 'L\'adresse email n\'est pas valide.']),
            ],
        ])
        ->add('password', RepeatedType::class, [
            'required' => true, // rend le champ obligatoire
            'type' => PasswordType::class,
            'first_options' => ['attr' => ['placeholder' => 'Entrez votre mot de passe']], // premier champ
            'second_options' => ['attr' => ['placeholder' => 'Veuillez confirmer votre mot de passe']], // deuxième champ
            'invalid_message' => 'Les mots de passe doivent correspondre.', // message d'erreur
            'constraints' => [ // ajoute des contraintes de validation pour ce champ
                new NotBlank(['message' => 'Veuillez entrer un mot de passe.']),
            ],
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
