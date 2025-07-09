<?php

namespace App\Form;

use App\Entity\Participant;
use App\Entity\Site;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ParticipantForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($options['include_profile']) {
            $builder
                ->add('username', null, [
                    'label' => 'Pseudo',
                    'attr' => [
                        'autofocus' => true,
                        ]
                ])
                ->add('firstName', null, [
                    'label' => 'Prénom',
                ])
                ->add('lastName', null, [
                    'label' => 'Nom',
                ])
                ->add('phone',null, [
                'label' => 'Téléphone',
                ])
                ->add('email', null, [
                    'label' => 'Email',
                ])
                ->add('site', EntityType::class, [
                    'class' => Site::class,
                    'choice_label' => 'name',
                    'placeholder' => 'Sélectionner un site',
                    'label' => 'Site de rattachement'
                ])
                ->add('profileImageFile', FileType::class, [
                    'label' => 'Ma photo',
                    'mapped' => false,
                    'required' => false,
                    'help' => 'Formats acceptés : JPG, PNG, WEBP (max 5 Mo)',
                    'constraints' => [
                        new File([
                            'maxSize' => '5M',
                            'mimeTypes' => ['image/jpeg', 'image/png',  'image/webp'],
                            'mimeTypesMessage' => 'Merci de télécharger une image valide (JPG, PNG, WEBP)',
                        ]),
                    ],
                ]);
        }

        if ($options['include_password']) {
            $builder
                ->add('password', PasswordType::class, [
                    'label' => 'Mot de passe',
                ])
                -> add('confirmPassword', PasswordType::class, [
                    'label' => 'Confirmation',
                    'mapped' => false,
                    'required' => true,
                ]);
        }

        $builder->add('submit', SubmitType::class, [
            'label' => 'Enregistrer'
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
            'include_profile' => true,
            'include_password' => true,
        ]);
    }
}
