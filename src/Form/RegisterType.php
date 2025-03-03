<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Validator\Constraints\Regex;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('firstname', TextType::class, [
            'label' => 'Prénom',
            'attr'=> [
                'placeholder' => "Veuillez renseigner votre prénom"
            ]
        ])
        ->add('lastname', TextType::class, [
            'label' => 'Nom',
            'attr'=> [
                'placeholder' => "Veuillez renseigner votre nom"
            ]
        ])
        ->add('email', EmailType::class,[
            'label' => 'Email',
            'attr' => [
                'placeholder' => "john@doe.com"
            ]
        ])
        ->add('password', RepeatedType::class, [ 
            'type' => PasswordType::class,
            'invalid_message' => 'Les deux mots de passe doivent être identiques !',
            'options' => [
                'attr' => [
                    'class' => 'password-field'
                ],
            ],
            'required' => true,
            'first_options'  => [
                'label' => 'Mot de passe',
                'attr'  => [
                    'placeholder' => "Veuillez saisir un mot de passe"
                ],
            ],
            'second_options' => [
                'label' => 'Confirmer votre mot de passe',
                'attr'  => [
                    'placeholder' => "Veuillez confirmer votre mot de passe"
                ],
            ],
            'constraints' => [
                new NotBlank([
                    'message' => 'Entrer un mot de passe SVP',
                ]),
                new Length([
                    // max length allowed by Symfony for security reasons
                    'max' => 4096
                ]),
                new Regex([
                    // 2 chiffres et un caractère spécial
                    'pattern' => '/^(?=.*\d.*\d)(?=.*[\W_]).{11,}$/',
                    'message' => 'Le mot de passe doit contenir au moins 11 caractères, dont 2 chiffres et 1 caractère spécial.',
                ]),
            ],
        ])
        ->add('isRgpd', CheckboxType::class,[
            'mapped' => false,
            'label' => "En m'inscrivant sur ce site, j'accepte que mes données personnelles soient utilisées",
            'constraints' => [
                new IsTrue([
                   'message' => "Vous devez accepter les termes liés à la politique de protection des données personnelles RGPD" 
                ]),
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
