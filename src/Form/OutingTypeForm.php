<?php

namespace App\Form;
use App\Form\LocationForm;
use App\Entity\Location;
use App\Entity\Site;
use App\Entity\Status;
use App\Entity\Participant;
use App\Entity\Outing;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class OutingTypeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                "label" => "Nom de la sortie : "])
            ->add('startingDateTime', DateTimeType::class, [
                "label" => "Date et heure de la sortie : "
            ])

            ->add('registrationDeadline', DateTimeType::class,[
                "label" => "Date limite d'inscription: "
            ])
            ->add('maxParticipants', IntegerType::class, [
                "label" => "Nombre de places : "
            ])
            ->add('duration', IntegerType::class, [
                "label" => "Durée : "
            ])
            ->add('outingDetails', TextareaType::class, [
                "label" => "Déscription et info : "
            ])

            ->add('site', EntityType::class, [
                'class' => Site::class,
                'choice_label' => 'name',
                'label' => 'Site :'
            ])

            ->add('location', LocationForm::class, [
                'label' => false // formulaire imbriqué, labels gérés à l’intérieur
            ]);
            /*
             * ->add('location', EntityType::class, [
                'class' => Location::class,
                'choice_label' => 'name', // La propriété de Location
                'label' => 'Lieu enregistré :'
            ])
            ->add('status', EntityType::class, [
                'class' => Status::class,
                'choice_label' => 'Etat',
            ])
            ->add('participants', EntityType::class, [
                'class' => Participant::class,
                'choice_label' => 'email', // ou autre
                'multiple' => true,
                'expanded' => true,
                'required' => false,
            ])*/


    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Outing::class,
        ]);
    }
}
