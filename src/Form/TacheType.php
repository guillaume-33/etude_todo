<?php

namespace App\Form;

use App\Entity\Projet;
use App\Entity\Tache;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class TacheType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('projet',TextType::class,[
                'mapped'=>false,
                'label'=>'Nouveau projet',
                'attr'=>['placeholder'=>'Nouveau projet']
            ] )

            ->add('titre', TextType::class,[
                'label'=>'nouvelle tâche',
                'attr'=>['placeholder'=>'Nouvelle tâche']
            ])
            ->add('message', TextareaType::class,[
                'attr'=>['placeholder'=>'Message au destinataire']
            ])
            ->add('date', DateType::class,[
                    'widget'=>'single_text',
                    'label'=> 'A faire pour le:'
            ])

            ->add('statut',ChoiceType::class, [
                'choices'=>[
                'A Faire'=>'A faire',
                'Fait'=>'Fait',
                'Fait partiellement'=>'Fait partiellement',
                'Non Fait'=>'Non fait'
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
