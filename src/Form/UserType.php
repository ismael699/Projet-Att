<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('firstName', TextType::class, [
            'required' => true, 
            'attr' => ['placeholder' => '...'],
        ])
        ->add('lastName', TextType::class, [
            'required' => true, 
            'attr' => ['placeholder' => '...'],
        ])
        ->add('roles', ChoiceType::class, [
            'label' => false, 
            'required' => true, 
            'choices' => [ 
                'Client' => 'ROLE_CLIENT',
                'Chauffeur' => 'ROLE_CHAUFFEUR',
            ],
            'multiple' => false, 
            'expanded' => true, 
        ])
            ->add('email', TextType::class, [
                'required' => false,
                'attr' => ['placeholder' => '@'],
            ])
            ->add('password', RepeatedType::class, [
                'required' => false, 
                'mapped' => false,
                'type' => PasswordType::class,
                'first_options' => ['attr' => ['placeholder' => 'Entrez votre mot de passe']],
                'second_options' => ['attr' => ['placeholder' => 'Confirmez votre mot de passe']],
                'invalid_message' => 'Les mots de passe doivent correspondre.', 
                'constraints' => [ 
                    new NotBlank(['message' => 'Veuillez entrer un mot de passe.']),
                    new Regex([
                        'pattern' => '/^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{9,}$/',
                        'message' => 'Le mot de passe doit contenir au moins 10 caractères, incluant au moins une majuscule, un chiffre et un signe spécial.',
                    ]),
                ],
            ])
            ->add('siren', TextType::class, [
                'required' => true, 
                'attr' => ['placeholder' => '...'],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label' => 'J\'ai pris connaissance et j\'accepte les conditions générales d\'utilisation.',
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter les règles pour continuer.',
                    ]),
                ],
            ])
            ;
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
