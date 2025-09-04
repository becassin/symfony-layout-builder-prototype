<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TextBlockConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Title',
                'required' => false,
                'attr' => ['class' => 'form-control', 'placeholder' => 'Use {{page.title}}, {{author.name}} for dynamic content']
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Content',
                'required' => false,
                'attr' => ['class' => 'form-control', 'rows' => 5, 'placeholder' => 'Use {{page.title}}, {{page.excerpt}}, {{author.name}}, {{author.city}} for dynamic content']
            ])
            ->add('css_class', TextType::class, [
                'label' => 'CSS Class',
                'required' => false,
                'attr' => ['class' => 'form-control']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
