<?php

namespace App\Mailer;

use Twig\Environment;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class Mailer
{

    private $twig;
    /**
     * @var MailerInterface
     */
    protected $mailer;

    /**
     * @var UrlGeneratorInterface
     */
    protected $router;

    /**
     * @var EngineInterface
     */
    protected $templating;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var ParameterBagInterface
     */
    protected $parameters;

    /**
     * Mailer constructor.
     *
     */
    public function __construct(\Swift_Mailer $mailer, UrlGeneratorInterface $router, Environment $twig, TranslatorInterface $translator, ParameterBagInterface $parameters)
    {
        $this->mailer = $mailer;
        $this->router = $router;
        $this->twig = $twig;
        $this->translator = $translator;
        $this->parameters = $parameters;
    }

    // Envoi du lien de validation d'inscritpion par mail
    public function sendRegistration(User $user)
    {
        $url = $this->router->generate(
            'app_registration_confirm',
            [
                'token' => $user->getConfirmationToken(),
            ],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $subject = 'Activation du compte'; //$this->translator->trans('registration.email.subject', ['%user%' => $user], 'security');
        $template = 'front/email/register.html.twig';
        // dd($this);
        // $from = [
        //     $this->parameters->get('configuration.')['from_email'] => $this->parameters->get('configuration')['name'],
        // ];
        $from = [
            $this->parameters->get('configuration')['from_email'] => $this->parameters->get('configuration')['name'],
        ];
        $to = $user->getEmail();
        // $from contient ["contact@alexisdev.info" => "Test"]
        // $to contient "alexis.decol@colombbus.org"

        // $body = $this->templating->render($template, [
        $body = $this->twig->render($template, [
            'user' => $user,
            'website_name' => $this->parameters->get('configuration')['name'],
            'confirmation_url' => $url,
        ]);



        $message = (new \Swift_Message())
            ->setSubject($subject)
            ->setFrom($from)
            ->setTo($to)
            ->setContentType("text/html")
            ->setBody($body);
        $this->mailer->send($message);
    }

    // Envoi d'une invitation utilisateur par mail
    public function sendInvitation(User $user, string $password)
    {
        $url = $this->router->generate(
            'app_registration_confirm',
            [
                'token' => $user->getConfirmationToken(),
            ],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $subject = $this->translator->trans('invitation.email.subject', [
            '%user%' => $user,
            '%website_name%' => $this->parameters->get('configuration')['name'],
        ], 'back_messages');
        $template = 'back/email/invite.html.twig';
        $from = [
            $this->parameters->get('configuration')['from_email'] => $this->parameters->get('configuration')['name'],
        ];
        $to = $user->getEmail();
        $body = $this->twig->render($template, [
            'user' => $user,
            'password' => $password,
            'website_name' => $this->parameters->get('configuration')['name'],
            'confirmation_url' => $url,
        ]);
        $message = (new \Swift_Message())
            ->setSubject($subject)
            ->setFrom($from)
            ->setTo($to)
            ->setContentType("text/html")
            ->setBody($body);
        $this->mailer->send($message);
    }
}
