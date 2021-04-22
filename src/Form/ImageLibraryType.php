<?php

namespace App\Form;

use App\Entity\TrickLibrary;
use App\Framework\Constantes;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ImageLibraryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lien', FileType::class, [
                'label' => false,
                'data_class' => null,
                'attr' => [
                    'aria-describedby' => 'basic-addon3',
                    'class' => 'form-control',
                    'style' => 'margin-bottom: 1rem',
                ],
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpg',
                            'image/jpeg'
                        ],
                        'mimeTypesMessage' => 'Merci d\'upload un fichier jpg ou jpeg',
                    ])
                ],
            ])
            ->add('type', HiddenType::class, ['data' => Constantes::LIBRARY_IMAGE]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TrickLibrary::class,
        ]);
    }
}
