<?php

namespace App\Form;

use App\Entity\Trick;
use App\Framework\Constantes;
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
                'choices' => Constantes::POSITION
            ])
            ->add('grabs', ChoiceType::class, [
                'choices' => Constantes::GRABS
            ])
            ->add('rotation', ChoiceType::class, [
                'choices' => Constantes::ROTATION
            ])
            ->add('flip', ChoiceType::class, [
                'choices' => Constantes::FLIP
            ])
            ->add('slide', ChoiceType::class, [
                'choices' => Constantes::SLIDE
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
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Merci d\'upload un fichier jpg, jpeg ou png',
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
