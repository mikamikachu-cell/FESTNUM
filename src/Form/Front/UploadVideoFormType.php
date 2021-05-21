<?php

namespace App\Form\Front;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
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
            ->add('video_title', TextType::class, [
                'label' => 'titre',
            ])
            ->add('video_description', TextType::class, [
                'label' => 'description',
            ])
            ->add('video_file', FileType::class, [
                'label' => 'video',
                'attr' => [
                    'accept' => 'video/*'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        // $resolver->setDefaults([
        //     'data_class' => User::class,
        //     'translation_domain' => 'security',
        // ]);
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
