<?php

namespace App\Form;

use App\Entity\Trick;
use App\Framework\Constantes;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class TrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'attr' => [
                    'aria-describedby' => 'basic-addon3',
                    'class' => 'form-control',
                    'placeholder' => 'Titre du trick...',
                    'style' => 'margin-bottom: 1rem'
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => [
                    'aria-describedby' => 'basic-addon3',
                    'class' => 'form-control',
                    'placeholder' => 'Description du trick...',
                    'style' => 'margin-bottom: 1rem'
                ]
            ])
            ->add('position', ChoiceType::class, [
                'choices' => Constantes::POSITION,
                'label' => 'Position',
                'attr' => [
                    'aria-describedby' => 'basic-addon3',
                    'class' => 'form-control',
                    'style' => 'margin-bottom: 1rem'
                ]
            ])
            ->add('grabs', ChoiceType::class, [
                'choices' => Constantes::GRABS,
                'label' => 'Grabs',
                'attr' => [
                    'aria-describedby' => 'basic-addon3',
                    'class' => 'form-control',
                    'style' => 'margin-bottom: 1rem'
                ]
            ])
            ->add('rotation', ChoiceType::class, [
                'choices' => Constantes::ROTATION,
                'label' => 'Rotation',
                'attr' => [
                    'aria-describedby' => 'basic-addon3',
                    'class' => 'form-control',
                    'style' => 'margin-bottom: 1rem'
                ]
            ])
            ->add('flip', ChoiceType::class, [
                'choices' => Constantes::FLIP,
                'label' => 'Flip',
                'attr' => [
                    'aria-describedby' => 'basic-addon3',
                    'class' => 'form-control',
                    'style' => 'margin-bottom: 1rem'
                ]
            ])
            ->add('slide', ChoiceType::class, [
                'choices' => Constantes::SLIDE,
                'label' => 'Slide',
                'attr' => [
                    'aria-describedby' => 'basic-addon3',
                    'class' => 'form-control',
                    'style' => 'margin-bottom: 1rem'
                ]
            ])
            ->add('videos', CollectionType::class, [
                'entry_type' => VideoLibraryType::class,
                'label' => false,
                'attr' => ['class' => 'form-control'],
                'allow_add' => true,
                'allow_delete' => true,
                'delete_empty' => true,
                'required' => false,
                'mapped' => false,
            ])
            ->add('images', CollectionType::class, [
                'entry_type' => ImageLibraryType::class,
                'label' => false,
                'attr' => ['class' => 'form-control'],
                'allow_add' => true,
                'allow_delete' => true,
                'delete_empty' => true,
                'required' => false,
                'mapped' => false,
                ])
            ->add('Enregistrer', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary',
                    'style' => 'margin-top: 1rem'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}
