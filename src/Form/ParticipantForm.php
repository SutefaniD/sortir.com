<?php

namespace App\Form;

use App\Entity\Participant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParticipantForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($options['include_profile']) {
            $builder
                ->add('lastName')
                ->add('firstName')
                ->add('phone')
                ->add('email');
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
