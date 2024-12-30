<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Recipe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\Form\Event\PostSubmitEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Sequentially;

class RecipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('slug', TextType::class, [
                'required' => false
            ])
            ->add('thumbnailFile', FileType::class) //utilise vichupload bundle
            //finalement ajouter dans Entity
            /* ->add('slug', TextType::class, [
                'required' => false,
                'constraints' => new Sequentially([ //ajouter contraints, sequentially permet de stoper validation si un constraint n'est pas rempli
                    new Length(null, 10),
                    new Regex('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', "Ceci n'est pas une slug valide")
                ])
            ]) */
            ->add('category', EntityType::class, [
                // looks for choices from this entity
                'class' => Category::class,
                'choice_label' => 'name',
                // used to render a select box, check boxes or radios
                // 'multiple' => true,
                // 'expanded' => true,
            ])
            ->add('content')
            ->add('duration')
            ->add('save', SubmitType::class)
            ->addEventListener(FormEvents::PRE_SUBMIT, function (PreSubmitEvent $event): void {
                $data = $event->getData();
                if (empty($data['slug'])) {
                    $slugger = new AsciiSlugger();
                    $data['slug'] = strtolower($slugger->slug($data['title']));
                    $event->setData($data);
                }
            })
            ->addEventListener(FormEvents::POST_SUBMIT, function (PostSubmitEvent $event): void {
                $data = $event->getData();
                if (!($data instanceof Recipe)) {
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
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
        ]);
    }
}
