<?php

namespace App\Form;

use App\Entity\Recipe;
// use PHPUnit\Util\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Form\Event\PostSubmitEvent;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Sequentially;
use Symfony\Component\Validator\Constraints\Type;

class RecipeType extends AbstractType
{
    public function __construct(private FormListenerFactory $formListenerFactory) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Title',
                // 'constraints' => [
                //     new Length(['min' => 10]),
                // ],
            ])
            ->add('slug', TextType::class, [
                'label' => 'Slug - auto',
                'required' => false,
                'attr' => ['class' => 'd-none'],
            ])
            ->add('content', TextType::class, [
                'label' => 'Content',
                // 'constraints' => [
                //     new Length(['min' => 10]),
                // ],
            ])
            // ->add('createdAt', null, [
            //     'widget' => 'single_text',
            // ])
            // ->add('updatedAt', null, [
            //     'widget' => 'single_text',
            // ])
            ->add('duration', TextType::class, [
                'label' => 'duration',
                // 'required' => false,
                // 'constraints' => new Sequentially([
                //     new NotBlank(),
                //     new Type('integer'),
                //     // new Regex(['pattern' => '/\d{1,2}:\d{2}/']),
                // ]),
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Submit',
                'attr' => ['class' => 'btn btn-success'],
            ])
            ->addEventListener(FormEvents::PRE_SUBMIT, $this->formListenerFactory->autoSlug('title'))
            // ->addEventListener(FormEvents::POST_SUBMIT, $this->autoCreatedAt(...))
            ->addEventListener(FormEvents::POST_SUBMIT, $this->formListenerFactory->attachTimestamps())
        ;
    }

    // public function autoSlug(PreSubmitEvent $event): void
    // {
    //     $data = $event->getData();
    //     // if (empty($data['slug'])) {
    //     $slugger = new AsciiSlugger();
    //     $data['slug'] = strtolower($slugger->slug($data['title']));
    //     $event->setData($data);
    //     // }
    // }

    // public function autoCreatedAt(PostSubmitEvent $event): void
    // {
    //     $recipe = $event->getData();
    //     $recipe->setCreatedAt(new \DateTimeImmutable());
    // }

    // public function attachTimestamps(PostSubmitEvent $event): void
    // {
    //     $recipe = $event->getData();
    //     if (!($recipe instanceof Recipe)) {
    //         return;
    //     }
    //     if ($recipe->getCreatedAt() === null) {
    //         $recipe->setCreatedAt(new \DateTimeImmutable());
    //     }
    //     $recipe->setUpdatedAt(new \DateTimeImmutable());
    // }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
        ]);
    }
}
