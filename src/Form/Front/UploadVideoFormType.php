<?php

namespace App\Form\Front;

use App\Entity\User;
use App\Entity\Video;
use Symfony\Component\Form\AbstractType;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class UploadVideoFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'data' => 'titre ' . Date('now'),

                'attr' => [
                    'placeholder' => 'Le titre du film'
                ],
            ])
            ->add('description', TextType::class, [
                'label' => 'Description',
                'data' => 'Default value',
                'attr' => [
                    'placeholder' => 'Décrivez le projet en quelques mots'
                ]
            ])
            ->add('file', VichFileType::class, [
                'label' => 'Video',
                'attr' => [
                    'accept' => 'image/*'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Video::class,
        ]);
    }
}
