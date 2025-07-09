<?php

namespace App\Form;
use App\Form\LocationForm;
use App\Entity\Location;
use App\Entity\Site;
use App\Entity\Status;
use App\Entity\Participant;
use App\Entity\Outing;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
            ->add('location', EntityType::class, [
                'class' => Location::class,
                'choice_label'  => function ($location) {
                    return $location->getName() . ', ' . $location->getStreet() . ' - ' . $location->getCity()->getName();
                },
                'label' => 'Lieux :',
                'placeholder' => '-- Veuillez choisir un lieu --'
            ])
            //->add('cancelReason', TextareaType::class, [
           //     "label" => "Motif :"
          //  ])

//            ->add('site', EntityType::class, [
//                'class' => Site::class,
//                'choice_label' => 'name',
//                'label' => 'Site :'
//            ]);


//            ->add('location', LocationForm::class, [
//                'label' => false // formulaire imbriqué, labels gérés à l’intérieur
//            ]);
        // les boutons d'envoie de formulaire'

            ->add('save', SubmitType::class, [
            'label' => 'Enregistrer',
            'attr' => ['class' => 'btn btn-success']
             ])
            ->add('publish', SubmitType::class, [
                'label' => 'Publier la sortie',
                'attr' => ['class' => 'btn btn-primary']
            ]);
//            ->add('cancel', SubmitType::class, [
//            'label' => 'Annuler la sortie',
//            'attr' => ['class' => 'btn btn-primary']
//            ])
//            ->add('delete', SubmitType::class, [
//            'label' => 'Supprimer la sortie',
//            'attr' => ['class' => 'btn btn-primary']
//        ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Outing::class,
        ]);
    }
}
