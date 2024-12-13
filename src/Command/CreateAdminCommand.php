<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

#[AsCommand(
    name: 'app:admin:create',
    description: 'Add a short description for your command',
)]
class CreateAdminCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('username', InputArgument::REQUIRED, 'Username')
            ->addArgument('email', InputArgument::REQUIRED, 'Email')
            ->addArgument('password', InputArgument::REQUIRED, 'Password')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {


        $io = new SymfonyStyle($input, $output);
        $password = $input->getArgument('password');
        $email = $input->getArgument('email');

        $userExists = $this->userRepository->findOneBy(['email' => $email]) !== null;
        if ($userExists) {
            $io->error('User already exists!');
            return Command::FAILURE;
        }

        $user = new User();
        $user->setEmail($email)
            ->setName($input->getArgument('username'))
            ->setRoles(['ROLE_ADMIN']);

        $passwordHash = $this->userPasswordHasher->hashPassword($user, $password);
        $user->setPassword($passwordHash);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return Command::SUCCESS;
    }
}
