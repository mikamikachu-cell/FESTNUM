<?php

namespace App\Form\Front;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\IsTrue;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'adresse email',
                'attr' => [
                    'placeholder' => 'exemple@exemple.fr'
                ]
            ])
            ->add('firstname', TextType::class, [
                'label' => 'prénom',
            ])
            ->add('lastname', TextType::class, [
                'label' => 'nom',
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Le contenu des deux champs doit être identique',
                'options' => ['attr' => ['class' => 'form-control']], // password-field 
                'required' => true,
                'first_options'  => ['label' => 'mot de passe'],
                'second_options' => ['label' => 'confirmez le mot de passe'],
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champs ne peut pas être vide',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Le mot de passe doit contenir au minimum 6 caractères',
                        'max' => 4096, // max length allowed by Symfony for security reasons
                    ]),
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label' => 'accepter les CGU',
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'registration.message.agree_terms_is_true',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'translation_domain' => 'security',
        ]);
    }
}
