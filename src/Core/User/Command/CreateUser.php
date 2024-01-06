<?php

namespace App\Core\User\Command;

use App\Common\Mailer\MailerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Core\User\Domain\Repository\UserRepositoryInterface;
use App\Core\User\Domain\User;

#[AsCommand(
    name: 'app:user:create',
    description: 'Tworzenie konta użytkownika',
)]
class CreateUser extends Command
{
    private MailerInterface $mailer;

    public function __construct(UserRepositoryInterface $userRepository, MailerInterface $mailer)
    {
        parent::__construct();
        $this->userRepository = $userRepository;
        $this->mailer = $mailer;
    }

    protected function configure(): void
    {
        $this->addArgument('email', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = $input->getArgument('email');

        $existingUser = $this->userRepository->findByEmail($email);

        if ($existingUser !== null) {
            $output->writeln('<error>Email jest już zajęty.</error>');
            return Command::FAILURE;
        }

        $user = new User($email);
        $user->setIsActive(false);

        $this->userRepository->save($user);
        $this->userRepository->flush();

        // Wysyłanie e-maila
        $mail = $this->sendRegistrationEmail($email);
        if($mail){
            $output->writeln('<info>E-mail został wysłany.</info>');
        }

        $output->writeln('<info>Użytkownik został stworzony.</info>');
        return Command::SUCCESS;
    }

    private function sendRegistrationEmail(string $email): bool
    {
        $subject = 'Rejestracja konta w systemie';
        $message = 'Zarejestrowano konto w systemie. Aktywacja konta trwa do 24h';

        $this->mailer->send($email, $subject, $message);

        return true;
    }
}
