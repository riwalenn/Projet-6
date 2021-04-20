<?php

namespace App\Form;

use App\Entity\TrickLibrary;
use App\Framework\Constantes;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VideoLibraryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lien', UrlType::class, [
                'attr' => [
                    'placeholder' => 'lien vidéo à ajouter...',
                    'aria-describedby' => 'basic-addon3',
                    'class' => 'form-control',
                    'style' => 'margin-bottom: 1rem',
                ],
                'label' => false
            ])
            ->add('type', HiddenType::class, ['data' => Constantes::LIBRARY_VIDEO]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TrickLibrary::class,
        ]);
    }
}
