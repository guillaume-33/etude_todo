<?php

namespace App\Form;

use App\Entity\Projet;
use App\Entity\Tache;
use App\Entity\User;
use Doctrine\DBAL\Types\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class TacheType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('projet',\Symfony\Component\Form\Extension\Core\Type\TextType::class,[
                'mapped'=>false,
                'label'=>'Nouveau projet'
            ] )

            //a prevoir pour modification projet
//            ->add('projet', EntityType::class,[
//                'class'=>Projet::class,
//                'choice_label'=>'titre',
//                'label'=>'Projet'
//            ])
            ->add('titre', \Symfony\Component\Form\Extension\Core\Type\TextType::class,[
                'label'=>'nouvelle tâche'
            ])
            ->add('message')
            ->add('date', DateType::class,[
                    'widget'=>'single_text',
                    'label'=> 'A faire pour le:'
            ])

//            ->add('expediteur', EntityType::class,[
//                    'class'=>User::class,
//                    'choice_label'=>'Prenom'
//                ])
            ->add('statut',ChoiceType::class, [
                'choices'=>[
                'A Faire'=>'A faire',
                'Fait'=>'Fait',
                'Fait partiellement'=>'Fait partiellement',
                'Impossible à faire'=>'Impossible a faire'
                ],
            ])
            ->add('destinataire', EntityType::class,[
                'class'=>User::class,
                'choice_label'=>'email'
            ])

            ->add('confirmer', SubmitType::class,[
                'attr' => ['class' => 'save']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tache::class,
        ]);
    }
}
