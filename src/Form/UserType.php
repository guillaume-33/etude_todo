<?php

namespace App\Form;

use App\Entity\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', TextType::class,[
                'attr'=>['placeholder'=>'Entrez votre Mail']
            ])
            ->add('password',RepeatedType::class,[

                'attr' => ['placeholder'=>'Entrez le magasin'],

                'first_options'  => ['label' => 'Mot de passe',  'attr' => ['placeholder'=>'Entrez le mot de passe'] ],
                'second_options' => ['label' => 'Confirmation mot de passe',  'attr' => ['placeholder'=>'Confirmer le mot de passe']],
                'invalid_message'=>'Le mot de passe ne correspond pas',
                'type'=>PasswordType::class,
            ])
            ->add('nom', TextType::class,[
                'attr'=>['placeholder'=>'Entrez votre Nom']
            ])
            ->add('prenom', TextType::class,[
                'attr'=>['placeholder'=>'Entrez votre Prénom']
            ])
            ->add('telephone', TextType::class, [
                'attr'=>['placeholder'=>'Entrez votre numéro de téléphone']
            ])
            ->add('magasin', TextType::class, [
                'attr'=>['placeholder'=>'Entrez le magasin']
            ])
            ->add('confirmer', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
