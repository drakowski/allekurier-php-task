<?php

declare(strict_types=1);

namespace App\Core\User\UserInterface\Cli;

use App\Core\User\Application\Command\CreateUser\CreateUserCommand;
use App\Core\User\Domain\Exception\UserNotFoundException;
use App\Core\User\Domain\Repository\UserRepositoryInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsCommand(
    name: 'app:user:create',
    description: 'Dodawanie nowego użytkownika'
)]
class CreateUser extends Command
{
    public function __construct(
        private readonly MessageBusInterface $bus,
        private readonly ValidatorInterface $validator,
        private readonly UserRepositoryInterface $userRepository,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = $input->getArgument('email');
        $command = new CreateUserCommand(
            $email,
        );

        $errors = $this->validator->validate($command);

        if ($errors->count() > 0) {
            $output->writeln(sprintf('<error>%s</error>', $errors));
            return Command::INVALID;
        }

        try {
            $this->userRepository->getByEmail($email);
            $output->writeln(sprintf('<error>%s</error>', "Podany użytkownik o adresie email $email już istnieje"));
            return Command::INVALID;
        } catch (UserNotFoundException) {
            $this->bus->dispatch($command);
            return Command::SUCCESS;
        }
    }

    protected function configure(): void
    {
        $this->addArgument('email', InputArgument::REQUIRED);
    }
}
