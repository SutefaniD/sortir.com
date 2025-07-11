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
        $readonly = $options['readonly'] ?? false;

        $attr = $readonly ? ['readonly' => true] : [];

        $builder
            // ->add('locationID', IntegerType::class, [
            //   'label' => 'ID du lieu'
            //])
            ->add('name', TextType::class, [
                'label' => 'Nom du lieu',
                'attr' => array_merge([
                    'autofocus' => true,
                    'id' => 'location_name',
                ], $attr)
            ])
            ->add('city', EntityType::class, [
                'class' => City::class,
                'choice_label' => 'name',
                'label' => 'Ville',
                'attr' => array_merge([
                    'id' => 'location_city',
                ], $attr)
            ])
            ->add('street', TextType::class, [
                'label' => 'Rue',
                'attr' => array_merge([
                    'id' => 'location_street',
                ], $attr)
            ])
            ->add('zipCode', TextType::class, [
                'label' => 'Code postal',
                'mapped' => false,
                'disabled' => false,
                'attr' => array_merge([
                    'id' => 'location_zipCode',
                ], $attr)
            ])
            ->add('latitude', TextType::class, [
                'label' => 'Latitude',
                'required' => false,
                'attr' => array_merge([
                    'id' => 'location_latitude',
                ], $attr)
            ])
            ->add('longitude', TextType::class, [
                'label' => 'Longitude',
                'required' => false,
                'attr' => array_merge([
                    'id' => 'location_longitude',
                ], $attr)
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Location::class,
            'readonly' => false,
        ]);
    }
}
