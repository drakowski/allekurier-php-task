<?php

declare(strict_types=1);

namespace App\Core\User\UserInterface\Cli;

use App\Core\User\Application\Command\CreateUser\CreateUserCommand;
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
    public function __construct(private readonly MessageBusInterface $bus, private readonly ValidatorInterface $validator)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $command = new CreateUserCommand(
            $input->getArgument('email'),
        );

        $errors = $this->validator->validate($command);

        if ($errors->count() > 0) {
            $output->writeln(sprintf('<error>%s</error>', $errors));
            return Command::INVALID;
        }

        $this->bus->dispatch($command);

        return Command::SUCCESS;
    }

    protected function configure(): void
    {
        $this->addArgument('email', InputArgument::REQUIRED);
    }
}
