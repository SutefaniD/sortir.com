<?php

namespace App\Form;

use App\Entity\Location;
use App\Entity\Outing;
use App\Entity\Participant;
use App\Entity\Site;
use App\Entity\Status;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OutingLocationForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('outing', OutingTypeForm::class, [
                'label' => false
            ])
//            ->add('location', LocationForm::class, [
//                'label' => false
//            ])
            ->add('location', EntityType::class, [
                'class' => Location::class,
                'choice_label' => 'name',
                'placeholder' => 'Sélectionnez un lieu existant',
                'required' => true,
                'label' => 'Lieu',
                'mapped' => true,
                'attr' => [
                    'data-action' => 'change->location#fetchDetails',
                ]
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer',
                'attr' => ['class' => 'btn btn-success']
            ])
            ->add('publish', SubmitType::class, [
                'label' => 'Publier la sortie',
                'attr' => ['class' => 'btn btn-primary']
            ]);
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null, // on passe un tableau, pas une entité !
            'csrf_protection' => true,
            'crsf_field_name' => '_token',
            'csrf_token_id' => 'outing_location_form'
        ]);
    }

}
