<?php

namespace App\Form;

use App\Entity\Trick;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class TrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', HiddenType::class)
            ->add('image')
            ->add('description')
            ->add('position', ChoiceType::class, [
                'choices' => Trick::POSITION
            ])
            ->add('grabs', ChoiceType::class, [
                'choices' => Trick::GRABS
            ])
            ->add('rotation', ChoiceType::class, [
                'choices' => Trick::ROTATION
            ])
            ->add('flip', ChoiceType::class, [
                'choices' => Trick::FLIP
            ])
            ->add('slide', ChoiceType::class, [
                'choices' => Trick::SLIDE
            ])
            ->add('image', FileType::class, [
                'label' => 'snowtricks-',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpg',
                        ],
                        'mimeTypesMessage' => 'Merci d\'upload un fichier jpg',
                    ])
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}
