<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

class ResetPasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!$options['with_token']) {
            $builder
                ->add('password', PasswordType::class, [
                    'label' => 'mot de passe courant',
                    'constraints' => [
                        new NotBlank([
                            'message' => 'registration.message.not_blank',
                        ]),
                        new Length([
                            'min' => 6,
                            'minMessage' => 'registration.message.password_length_min',
                            // max length allowed by Symfony for security reasons
                            'max' => 4096,
                        ]),
                        new UserPassword([
                            'message' => 'erreur de mot de passe',
                        ])
                    ],
                ]);
        }
        $builder
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe ne sont pas identiques',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => ['label' => 'nouveau mot de passe'],
                'second_options' => ['label' => 'répétez le nouveau mot de passe'],
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'registration.message.password_not_blank',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Le mot de passe doit faire au minimum 6 caractères',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'with_token' => false,
            'translation_domain' => 'security',
        ]);
    }
}
