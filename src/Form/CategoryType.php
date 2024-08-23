<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Recipe;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Form\Event\PostSubmitEvent;
use Symfony\Component\Form\Event\PreSubmitEvent;


class CategoryType extends AbstractType
{

    public function __construct(private FormListenerFactory $formListenerFactory) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Name',
                'constraints' => [
                    new NotBlank(),
                ],
                'empty_data' => '',
            ])
            ->add('slug', TextType::class, [
                'label' => 'Slug - auto',
                'required' => false,
                'attr' => ['class' => 'd-none'],
            ])
            // ->add('Recipes', EntityType::class, [
            //     'class' => Recipe::class,
            //     'choice_label' => 'title',
            //     'multiple' => true,
            //     'expanded' => true,
            //     'by_reference' => false,
            // ])
            ->add('submit', SubmitType::class, [
                'label' => 'Submit',
                'attr' => ['class' => 'btn btn-success'],
            ])
            ->addEventListener(FormEvents::PRE_SUBMIT, $this->formListenerFactory->autoSlug('name'))
            ->addEventListener(FormEvents::POST_SUBMIT, $this->formListenerFactory->attachTimestamps())
        ;
    }

    // public function autoSlug(PreSubmitEvent $event): void
    // {
    //     $data = $event->getData();
    //     $slugger = new AsciiSlugger();
    //     $data['slug'] = strtolower($slugger->slug($data['name']));
    //     $event->setData($data);
    // }



    // public function attachTimestamps(PostSubmitEvent $event): void
    // {
    //     $category = $event->getData();
    //     if (!($category instanceof Category)) {
    //         return;
    //     }
    //     if ($category->getCreatedAt() === null) {
    //         $category->setCreatedAt(new \DateTimeImmutable());
    //     }
    //     $category->setUpdatedAt(new \DateTimeImmutable());
    // }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}
