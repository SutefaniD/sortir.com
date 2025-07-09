<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\Location;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\EventDispatcher\Event;

class LocationForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // ->add('locationID', IntegerType::class, [
            //   'label' => 'ID du lieu'
            //])
            ->add('name', TextType::class, [
                'label' => 'Nom du lieu',
                'attr' => [
                    'autofocus' => true,
                    'id' => 'location_name'
                ]
            ])
            ->add('city', EntityType::class, [
                'class' => City::class,
                'choice_label' => 'name',
                'label' => 'Ville',
                'attr' => [
                    'id' => 'city_name'
                ]
            ])
            ->add('street', TextType::class, [
                'label' => 'Rue',
                'attr' => [
                    'id' => 'location_street'
                ]
            ])
            ->add('zipCode', TextType::class, [
                'class' => City::class,
                'label' => 'Code postal',
                'mapped' => false,
                'disabled' => false,
                'attr' => [
                    'id' => 'city_zip'
                ]
            ])
            ->add('latitude', TextType::class, [
                'label' => 'Latitude',
                'required' => false,
                'attr' => [
                    'id' => 'location_latitude'
                ]

            ])
            ->add('longitude', TextType::class, [
                'label' => 'Longitude',
                'required' => false,
                'attr' => [
                    'id' => 'location_longitude'
                ]
            ]);
//            ->add('submit', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Location::class,
        ]);
    }
}
