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
        'label' => 'Name',
        'required' => true
      ])
      ->add('email', EmailType::class, [
        'label' => 'Email Adresse',
        'required' => true
      ])
      ->add('service', ChoiceType::class, [
        'label' => 'To service',
        'required' => true,
        'choices'  => [
          'Commercial' => 'commercial@exemple.com',
          'Technical support' => 'Support@exemple.com',
          'Recruitment' => 'recrutement@exemple.com',
        ]
      ])
      ->add('message', TextareaType::class, [
        'required' => true
      ])
      ->add('Send', SubmitType::class)
    ;
  }
}
