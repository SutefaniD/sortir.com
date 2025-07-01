<?php

namespace App\Form;

use App\DTO\OutingFilterDTO;
use App\Entity\Site;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterForm extends AbstractType
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
            ->add('startDate', DateType::class, [
                'label' => 'Entre ',
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('endDate', DateType::class, [
                'label' => ' et ',
                'widget' => 'single_text',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => OutingFilterDTO::class,
        ]);
    }
}
