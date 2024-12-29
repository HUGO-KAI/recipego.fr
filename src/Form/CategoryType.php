<?php

namespace App\Form;

use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\Form\Event\PostSubmitEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('slug', TextType::class, [
                'required'=>false
            ])
            ->add('save', SubmitType::class)
            ->addEventListener(FormEvents::PRE_SUBMIT, function (PreSubmitEvent $event): void {
                $data = $event->getData();
                if (empty($data['slug'])) {
                    $slugger = new AsciiSlugger();
                    $data['slug'] = strtolower($slugger->slug($data['name']));
                    $event->setData($data);
                }
            })
            ->addEventListener(FormEvents::POST_SUBMIT, function (PostSubmitEvent $event): void {
                $data = $event->getData();
                if (!($data instanceof Category)) {
                    return;
                }

                if (empty($data->createdAt)) {
                    $createdAt = new \DateTimeImmutable();
                    $data->setCreatedAt($createdAt);
                }
                if (empty($data->updatedAt)) {
                    $updatedAt = new \DateTimeImmutable();
                    $data->setUpdatedAt($updatedAt);
                }
            });
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}
