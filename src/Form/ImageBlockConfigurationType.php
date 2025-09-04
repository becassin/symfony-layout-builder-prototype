<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImageBlockConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('src', TextType::class, [
                'label' => 'Image URL',
                'attr' => ['class' => 'form-control']
            ])
            ->add('alt', TextType::class, [
                'label' => 'Alt Text',
                'required' => false,
                'attr' => ['class' => 'form-control']
            ])
            ->add('caption', TextType::class, [
                'label' => 'Caption',
                'required' => false,
                'attr' => ['class' => 'form-control']
            ])
            ->add('width', TextType::class, [
                'label' => 'Width (CSS)',
                'required' => false,
                'attr' => ['class' => 'form-control', 'placeholder' => 'e.g., 300px, 50%']
            ])
            ->add('height', TextType::class, [
                'label' => 'Height (CSS)',
                'required' => false,
                'attr' => ['class' => 'form-control', 'placeholder' => 'e.g., 200px, auto']
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
