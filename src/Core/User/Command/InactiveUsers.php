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
    name: 'app:user:inactive-found-mails',
    description: 'Wyszukiwanie emaili nieaktywnych użytkowników',
)]
class InactiveUsers extends Command
{
    public function __construct(UserRepositoryInterface $userRepository)
    {
        parent::__construct();
        $this->userRepository = $userRepository;
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $users = $this->userRepository->findInactiveUsers(false);

        foreach ($users as $user) 
        {
            $output->writeln($user->getEmail());
        }

        return Command::SUCCESS;
    }
}
