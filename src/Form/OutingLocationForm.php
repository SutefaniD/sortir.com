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
            ->add('chooseLocation', EntityType::class, [
                "class" => Location::class,
                "choice_label" => "name",
                "placeholder" => "Choisir un lieu",
                'mapped' => false,
            ])
            ->add('newLocation', LocationForm::class, [
                'label' => false,
                'required' => false,
            ])

            ->add('create', SubmitType::class, [
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
