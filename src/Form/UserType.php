<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('roles', ChoiceType::class, [
            'label' => false, // cache le label
            'required' => true, // rend le champ obligatoire
            'choices' => [ // définit les choix possibles 
                'Client' => 'ROLE_CLIENT',
                'Chauffeur' => 'ROLE_CHAUFFEUR',
            ],
            'multiple' => false, // refuse la sélection de choix multiple
            'expanded' => true, // affiche les choix sous forme de case à cocher
        ])
            ->add('email', TextType::class, [
                'required' => false,
                'attr' => ['placeholder' => 'email@gmail.com'], // ajoute un attribut HTML pour le placeholder
            ])
            ->add('password', RepeatedType::class, [
                'required' => false, 
                'mapped' => false,
                'type' => PasswordType::class,
                'first_options' => ['attr' => ['placeholder' => 'Entrez votre mot de passe']], 
                'second_options' => ['attr' => ['placeholder' => 'Veuillez confirmer votre mot de passe']], 
                'invalid_message' => 'Les mots de passe doivent correspondre.', // message d'erreur
                'constraints' => [ // ajout de contraintes de validation pour ce champ
                    new NotBlank(['message' => 'Veuillez entrer un mot de passe.']),
                    new Regex([
                        'pattern' => '/^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{9,}$/',
                        'message' => 'Le mot de passe doit contenir au moins 10 caractères, incluant au moins une majuscule, un chiffre et un signe spécial.',
                    ]),
                ],
            ])
            ->add('siren', TextType::class, [
                'required' => true, 
                'attr' => ['placeholder' => 'Entrez votre numéro de siren'], 
            ]);

            $builder->get('roles')
            ->addModelTransformer(new CallbackTransformer(
            function ($rolesArray) {
                // transform the array to a string
                return count($rolesArray)? $rolesArray[0]: null;
            },
            function ($rolesString) {
                // transform the string back to an array
                return [$rolesString];
            }
        ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
