<?php

namespace App\Controller\Front;

use App\Entity\User;
use App\Mailer\Mailer;
use App\Form\Front\RegistrationFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractController
{

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @Route("/register", name="front_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, Mailer $mailer): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $user->setEnabled(false);
            $user->setConfirmationToken(utf8_encode(random_bytes(24))); // ajout de utf8_encode pour l'envoi en BDD
            $user->setLastLoginAt(new \DateTime());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $mailer->sendRegistration($user);

            $msg = $this->translator->trans('Un lien d\'activation vous a été envoyé par mail.', ['%email%' => $user->getEmail(),], 'security');
            $this->addFlash('info', $msg);

            return $this->redirectToRoute('app_login');
        }
        return $this->render('front/registration/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
