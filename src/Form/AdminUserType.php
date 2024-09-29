<?php

namespace App\Form;

use App\Entity\User;
use App\Form\AdminUserInfosType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class AdminUserType extends AbstractType
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
        ->add('roles', ChoiceType::class, [
            'label' => false, // cache le label
            'required' => true, // rend le champ obligatoire
            'choices' => [ // définit les choix possibles 
                'Client' => 'ROLE_CLIENT',
                'Chauffeur' => 'ROLE_CHAUFFEUR',
            ],
            'multiple' => false, // permet la sélection de choix multiple ( à modifier !)
            'expanded' => true, // affiche les choix sous forme de case à cocher

            'constraints' => [ // ajoute une contrainte de validation pour s'assurer que le champ n'est pas vide
                new NotBlank(['message' => 'Veuillez choisir un rôle.']),
            ],
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
                'mapped' => false,
                'type' => PasswordType::class,
                'first_options' => ['attr' => ['placeholder' => 'Entrez votre mot de passe']], // premier champ
                'second_options' => ['attr' => ['placeholder' => 'Veuillez confirmer votre mot de passe']], // deuxième champ
                'invalid_message' => 'Les mots de passe doivent correspondre.', // message d'erreur
                'constraints' => [ // ajoute des contraintes de validation pour ce champ
                    new NotBlank(['message' => 'Veuillez entrer un mot de passe.']),
                ],
            ])
            ->add('siren', TextType::class, [
                'required' => true, // rend le champ obligatoire
                'attr' => ['placeholder' => 'Entrez votre numéro de siren'], // ajoute un attribut HTML pour le placeholder
                'constraints' => [ // ajoute des contraintes de validation pour ce champ
                    new NotBlank(['message' => 'Veuillez entrer un numéro de Siren.']),
                    new Regex([ // ajoute un regex
                        'pattern' => '/^\d{9}$/',
                        'message' => 'Le siren doit être composé de 9 chiffres.',
                    ]),
                ],
            ])
            ->add('UserInfos', AdminUserInfosType::class); // Imbrication du formulaire UserInfos;

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
