<?php

namespace App\Form;

use App\Entity\UserInfos;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\File as FileConstraint;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class AdminUserInfosType extends AbstractType
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
        ->add('phoneNumber', TextType::class, [
            'required' => true, // rend le champ obligatoire
            'attr' => ['placeholder' => '06 ..'], // ajoute un attribut HTML pour le placeholder
        ])
        ->add('photoFile', FileType::class, [
            'required' => true, // rend le champ obligatoire
            'attr' => ['class' => 'file-input'], // ajoute un attribut HTML pour le placeholder
            'constraints' => [
                new File([
                    'mimeTypes' => [ // accepte les images jpeg et png ou un document pdf
                        'image/jpeg',
                        'image/png',
                        'application/pdf'
                    ],
                    'mimeTypesMessage' => 'Merci de télécharger une image valide (jpeg, png) ou un fichier PDF.',
                ])
            ],
        ])
        ->add('drivingLicenseFile', FileType::class, [
            'required' => true, // champ obligatoire
            'attr' => ['class' => 'file-input'], // ajoute un attribut HTML pour le placeholder
            'constraints' => [
            new NotBlank(['message' => 'Veuillez télécharger un document en PDF.']), // contrainte
            new FileConstraint([
                'mimeTypes' => ['application/pdf'], // PDF uniquement
                'mimeTypesMessage' => 'Veuillez télécharger un document en PDF.', 
                ]),
            ],
        ])
        ->add('file', FileType::class, [
            'required' => true, // rend le champ obligatoire
            'attr' => ['class' => 'file-input'], // ajoute un attribut HTML pour la classe
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
            'data_class' => UserInfos::class,
        ]);
    }
}
