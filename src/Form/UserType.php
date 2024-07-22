<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Validator\Constraints\File as FileConstraint;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('roles', ChoiceType::class, [
                'label' => false, // désactive l'affichage du label
                'required' => true, // rend le champ obligatoire
                'choices' => [ // définit les choix possibles 
                    'Client' => 'ROLE_CLIENT',
                    'Chauffeur' => 'ROLE_CHAUFFEUR',
                ],
                'multiple' => true, // permetla sélection de choix multiple ( à modifier !)
                'expanded' => true, // affiche les choix sous forme de case à cocher
                'constraints' => [ // ajoute une contrainte de validation pour s'assurer que le champ n'est pas vide
                    new NotBlank(['message' => 'Veuillez choisir un rôle.']),
                ],
            ])
            ->add('email', TextType::class, [
                'label' => false, // désactive l'affichage du label
                'required' => true, // rend le champ obligatoire
                'attr' => ['placeholder' => 'Adresse email'], // ajoute un attribut HTML pour le placeholder
                'constraints' => [ // ajoute des contraintes de validation pour ce champ
                    new NotBlank(['message' => 'Veuillez entrer une adresse email.']),
                    new Email(['message' => 'L\'adresse email n\'est pas valide.']),
                ],
            ])
            ->add('password', RepeatedType::class, [
                'label' => false, // désactive l'affichage du label
                'required' => true, // rend le champ obligatoire
                'type' => PasswordType::class,
                'first_options' => ['label' => 'Mot de passe', 'attr' => ['placeholder' => 'Mot de passe']], // premier champ
                'second_options' => ['label' => 'Confirmer le mot de passe', 'attr' => ['placeholder' => 'Confirmer le mot de passe']], // deuxième champ
                'invalid_message' => 'Les mots de passe doivent correspondre.', // message d'erreur
                'constraints' => [ // ajoute des contraintes de validation pour ce champ
                    new NotBlank(['message' => 'Veuillez entrer un mot de passe.']),
                ],
            ])
            ->add('siren', TextType::class, [
                'label' => false, // désactive l'affichage du label
                'required' => true, // rend le champ obligatoire
                'attr' => ['placeholder' => 'Numéro de Siren'], // ajoute un attribut HTML pour le placeholder
                'constraints' => [ // ajoute des contraintes de validation pour ce champ
                    new NotBlank(['message' => 'Veuillez entrer un numéro de Siren.']),
                    new Regex([ // ajoute un regex
                        'pattern' => '/^\d{9}$/',
                        'message' => 'Le siren doit être composé de 9 chiffres.',
                    ]),
                ],
            ])
            ->add('file', FileType::class, [
                'label' => false, // désactive l'affichage du label
                'required' => true, // rend le champ obligatoire
                'constraints' => [ // ajoute des contraintes de validation pour ce champ
                    new NotBlank(['message' => 'Veuillez télécharger un document en PDF.']),
                    new FileConstraint([ // vérifie que le fichier téléchargé est bien un PDF
                        'mimeTypes' => ['application/pdf'],
                        'mimeTypesMessage' => 'Veuillez télécharger un document en PDF.',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
