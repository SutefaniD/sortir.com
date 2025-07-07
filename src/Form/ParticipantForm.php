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
                ->add('lastName', null, [
                    'attr' => [
                        'autofocus' => true,
                    ]
                ])
                ->add('firstName')
                ->add('username')
                ->add('phone')
                ->add('email')
                ->add('site', EntityType::class, [
                    'class' => Site::class,
                    'choice_label' => 'name',
                    'placeholder' => 'Choose a site',
                    'label' => 'Attachment site'
                ])
                ->add('profileImageFile', FileType::class, [
                    'label' => 'Photo de profil (JPG, PNG, WEBP)',
                    'mapped' => false,
                    'required' => false,
                    'constraints' => [
                        new File([
                            'maxSize' => '5M',
                            'mimeTypes' => ['image/jpeg', 'image/png',  'image/webp'],
                            'mimeTypesMessage' => 'Merci de télécharger une image valide',
                        ]),
                    ],
                ]);
        }

        if ($options['include_password']) {
            $builder
                ->add('password', PasswordType::class)
                -> add('confirmPassword', PasswordType::class, [
                    'mapped' => false,
                    'required' => true,
                ]);
        }

        $builder->add('submit', SubmitType::class)
        ;
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
