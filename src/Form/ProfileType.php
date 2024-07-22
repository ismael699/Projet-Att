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

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('firstName', TextType::class, [
            'label' => 'Prénom',
            'label' => false, // désactive l'affichage du label
            'required' => true, // rend le champ obligatoire
            'attr' => ['placeholder' => 'Prénom'], // ajoute un attribut HTML pour le placeholder
        ])
        ->add('lastName', TextType::class, [
            'label' => 'Nom',
            'label' => false, // désactive l'affichage du label
            'required' => true, // rend le champ obligatoire
            'attr' => ['placeholder' => 'Nom'], // ajoute un attribut HTML pour le placeholder
        ])
        ->add('phoneNumber', TextType::class, [
            'label' => 'Téléphone',
            'label' => false, // désactive l'affichage du label
            'required' => true, // rend le champ obligatoire
            'attr' => ['placeholder' => 'Téléphone'], // ajoute un attribut HTML pour le placeholder
        ])
        ->add('photoFile', FileType::class, [
            'label' => 'Photo (JPG, PNG ou PDF)',
            'label' => false, // désactive l'affichage du label
            'required' => true, // rend le champ obligatoire
            'attr' => ['placeholder' => 'Photo de profile'], // ajoute un attribut HTML pour le placeholder
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
            'label' => 'Document en PDF', 
            'label' => false, // désactive l'affichage du label
            'required' => true, // champ obligatoire
            'attr' => ['placeholder' => 'Permis de conduire'], // ajoute un attribut HTML pour le placeholder
            'constraints' => [
            new NotBlank(['message' => 'Veuillez télécharger un document en PDF.']), // contrainte
            new FileConstraint([
                'mimeTypes' => ['application/pdf'], // PDF uniquement
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
