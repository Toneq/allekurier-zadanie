<?php

namespace App\Core\Invoice\UserInterface\Cli;

use App\Core\Invoice\Application\Command\CreateInvoice\CreateInvoiceCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Core\User\Domain\Repository\UserRepositoryInterface;

#[AsCommand(
    name: 'app:invoice:create',
    description: 'Dodawanie nowej faktury'
)]
class CreateInvoice extends Command
{
    public function __construct(private readonly MessageBusInterface $bus, private readonly UserRepositoryInterface $userRepository)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = $input->getArgument('email');
        $amount = $input->getArgument('amount');

        $user = $this->userRepository->findByEmail($email);

        if($user === null) {
            $output->writeln('<error>Użytkownik nie istnieje.</error>');
            return Command::FAILURE;
        }

        if (!$user->getIsActive()) {
            $output->writeln('<error>Użytkownik nie jest aktywny.</error>');
            return Command::FAILURE;
        }

        $this->bus->dispatch(new CreateInvoiceCommand(
            $input->getArgument('email'),
            $input->getArgument('amount')
        ));

        $output->writeln('<info>Faktura została wystawiona.</info>');
        return Command::SUCCESS;
    }

    protected function configure(): void
    {
        $this->addArgument('email', InputArgument::REQUIRED);
        $this->addArgument('amount', InputArgument::REQUIRED);
    }
}
