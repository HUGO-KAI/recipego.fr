<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'required' => true
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse mail',
                'required' => true
            ])
            ->add('service', ChoiceType::class, [
                'label' => 'Au service',
                'required' => true,
                'choices'  => [
                    'Commercial' => 'commercial@exemple.com',
                    'Support technique' => 'Support@exemple.com',
                    'Recrutement' => 'recrutement@exemple.com',
                ]
            ])
            ->add('message', TextareaType::class, [
                'required' => true
            ])
            ->add('envoyer', SubmitType::class)
        ;
    }
}
