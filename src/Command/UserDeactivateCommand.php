<?php

namespace App\Command;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UserDeactivateCommand extends Command
{
    protected static $defaultName = 'app:user:deactivate';
    protected static $defaultDescription = 'Add a short description for your command';

    /**
     *
     * @var EntityManagerInterface
     */
    private $em;

    /**
     *
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(EntityManagerInterface $em, UserRepository $userRepository)
    {
        $this->em = $em;
        $this->userRepository = $userRepository;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('')
            ->addArgument('email', InputArgument::REQUIRED, 'the email')
            ->setHelp(implode("\n", [
                '',
            ]));
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        // On crée une liste de questions de type Symfony\Component\Console\Question\Question;
        $questions = [];

        if (!$input->getArgument('email')) {
            // On demande l’adresse mail de l’utilisateur à créer ou modifier;
            $question = new Question('Please give the email:');
            $question->setValidator(function ($email) {
                // On valide que la chaîne n’est pas vide sinon on lance une exception qui aura l’effet de redemander l’adresse mail;
                if (empty($email)) {
                    throw new \Exception('email can not be empty');
                }

                // On valide que l’utilisateur avec cette adresse existe ou pas selon la commande;
                if (!$this->userRepository->findOneByEmail($email)) {
                    throw new \Exception('No user found with this email');
                }

                return $email;
            });
            $questions['email'] = $question;
        }

        // On lance la boucle pour exécuter les questions;
        foreach ($questions as $name => $question) {
            $answer = $this->getHelper('question')->ask($input, $output, $question);
            $input->setArgument($name, $answer);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');
        $user = $this->userRepository->findOneByEmail($email);

        $user->setEnabled(false);
        $this->em->flush();

        $io->success('User "%s" has been activated', $email);
        return 0;
    }
}
