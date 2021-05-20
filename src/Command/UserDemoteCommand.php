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

class UserDemoteCommand extends Command
{
    protected static $defaultName = 'app:user:demote';
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
            ->addArgument('role', InputArgument::REQUIRED, 'the new role')
            ->setHelp(implode("\n", [
                'The <info>app:user:demote</info> command deactivate a role to a user:',
                '<info>php %command.full_name% martin.gilbert@dev-fusion.com</info>',
                'This interactive shell will first ask you for a role.',
                'You can alternatively specify the role as a second argument:',
                '<info>php %command.full_name% martin.gilbert@dev-fusion.com ROLE_ADMIN</info>',
            ]));
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        // On crée une liste de questions de type Symfony\Component\Console\Question\Question;
        $questions = [];

        if (!$input->getArgument('email')) { // QUESTION 1
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

        if (!$input->getArgument('role')) { // QUESTION 2
            $question = new Question('Please enter the role to deactivate:');
            $question->setValidator(function ($role) {
                if (empty($role)) {
                    throw new \Exception('role can not be empty');
                }
                return $role;
            });
            $questions['role'] = $question;
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
        $role = $input->getArgument('role');

        // On demande l’utilisateur au repository;
        $user = $this->userRepository->findOneByEmail($email);
        $roles = $user->getRoles();

        if (!in_array($role, $roles)) {
            $io->error(sprintf("The user %s has not role %s.", $email, $role));
            return 1;
        } else {
            array_splice($roles, array_search($role, $roles), 1);
            $user->setRoles($roles);
            $this->em->flush();
            $io->success(sprintf('The role %s has been removed to user %s.', $role, $email));
            return 0;
        }

        $user->setEnabled(true);
        $this->em->flush();

        $io->success('User "%s" has been activated', $email);
        return 0;
    }
}
