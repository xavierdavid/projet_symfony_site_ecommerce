<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class ResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            
            ->add('new_password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les deux mots de passe doivent être identiques',
                'options'  => [
                    'attr' => ['class' => 'password-field']
                ],
                'required' => true,
                'first_options' => [
                    'label' => "Nouveau mot de passe",
                    'attr'  => [
                        'placeholder' => "Veuillez saisir un nouveau mot de passe"
                    ]
                ],
                'second_options' => [
                    'label' => "Confirmer votre nouveau mot de passe",
                    'attr'  => [
                        'placeholder' => "Veuillez confirmer votre nouveau mot de passe"
                    ]
                ],
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => "Entrer un mot de passe SVP"
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => "Votre mot de passe doit comporter au minimum {{limit}} caractères.",
                        // max length allowed by Symfony for security reasons
                        'max' => 496
                    ])
                ]
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