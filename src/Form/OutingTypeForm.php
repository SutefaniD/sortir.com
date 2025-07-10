<?php

namespace App\Form;
use App\Entity\Location;
use App\Entity\Outing;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
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
            ->add('registrationDeadline', DateType::class,[
                "label" => "Date limite d'inscription : "
            ])
            ->add('maxParticipants', IntegerType::class, [
                "label" => "Nombre de places : "
            ])
            ->add('duration', IntegerType::class, [
                "label" => "Durée : "
            ])
            ->add('outingDetails', TextareaType::class, [
                "label" => "Description et infos : "
            ]);



//            ->add('location', EntityType::class, [
//                'class' => Location::class,
//                'choice_label'  => function ($location) {
//                    return $location->getName() . ' - ' . $location->getCity()->getName();
//                },
//                'label' => 'Lieu :'
//            ]);

            // les boutons d'envoie de formulaire'

//            ->add('save', SubmitType::class, [
//                'label' => 'Enregistrer',
//                'attr' => ['class' => 'btn btn-success']
//            ])
//            ->add('publish', SubmitType::class, [
//                'label' => 'Publier la sortie',
//                'attr' => ['class' => 'btn btn-primary']
//            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Outing::class,
        ]);
    }

}
