<?php

namespace App\Form;

use App\Entity\Projet;
use App\Entity\Tache;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TacheType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre')
            ->add('message')
            ->add('date', DateType::class,[
                    'widget'=>'single_text'
            ])
            ->add('statut')
            ->add('destinataire', EntityType::class,[
                'class'=>User::class,
                'choice_label'=>'email'
            ])
            ->add('expediteur', EntityType::class,[
                    'class'=>User::class,
                    'choice_label'=>'prenom'
                ])
            ->add('projet', EntityType::class,[
                'class'=>Projet::class,
                'choice_label'=>'titre'
            ])
            ->add('confirmer', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tache::class,
        ]);
    }
}
