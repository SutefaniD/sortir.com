<?php

namespace App\Form;

use App\DTO\SearchFormDTO;
use App\Entity\Site;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('site', EntityType::class, [
                'class' => Site::class,
                'choice_label' => 'name',
                'placeholder' => 'Tous les sites',
                'required' => false,
            ])
            ->add('outingName', SearchType::class, [
                'label' => "Le nom de la sortie contient :",
                'required' => false,
                'attr' => [
                    'placeholder' => 'Rechercher une sortie',
                ]
            ])
            ->add('startDate', DateTimeType::class, [
                'label' => 'Entre ',
                'widget' => 'single_text',
                //ou
                //'date_widget' => 'choice', //ou 'single_text'
                //'time_widget' => 'choice',
                'required' => false,
            ])
            ->add('endDate', DateTimeType::class, [
                'label' => ' et ',
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('isOrganizer', CheckboxType::class, [
                'label' => "Sorties dont je suis l'organisateur/trice",
                'required' => false,
            ])
            ->add('isParticipant', CheckboxType::class, [
            'label' => "Sorties auxquelles je suis inscrit/e",
            'required' => false,
            ])
            ->add('isNotParticipant', CheckboxType::class, [
                'label' => "Sorties auxquelles je ne suis pas inscrit/e",
                'required' => false,
            ])
            ->add('pastOutings', CheckboxType::class, [
                'label' => "Sorties passées",
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SearchFormDTO::class,
        ]);
    }
}
