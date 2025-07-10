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
use Symfony\Component\OptionsResolver\OptionsResolver;

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
                   ]
            ])
            ->add('city', EntityType::class, [
                'class' => City::class,
                'choice_label' => 'name',
                'label' => 'Ville',
                'placeholder' => '-- Veuillez choisir une ville --'
            ])
            ->add('street', TextType::class, [
                'label' => 'Rue'
            ])
//            ->add('zipCode', TextType::class, [
//                'label' => 'Code postal',
//                'mapped' => false,
//                'disabled' => false,
//            ])
            ->add('latitude', TextType::class, [
                'label' => 'Latitude'
            ])
            ->add('longitude', TextType::class, [
                'label' => 'Longitude'
            ])
            ->add('submit', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Location::class,
        ]);
    }
}
