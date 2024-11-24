<?php

declare(strict_types=1);

namespace App\Core\User\UserInterface\Cli;

use App\Common\Bus\QueryBusInterface;
use App\Core\User\Application\DTO\UserEmailDTO;
use App\Core\User\Application\Query\GetUsersByInactiveStatus\GetUsersByInactiveStatusQuery;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:user:get-inactive',
    description: 'Pobieranie nie aktywnych użytkowników'
)]
class GetInactiveUsers extends Command
{
    public function __construct(
        private readonly QueryBusInterface $bus,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $users = $this->bus->dispatch(new GetUsersByInactiveStatusQuery());

        /** @var UserEmailDTO $user */
        foreach ($users as $user) {
            $output->writeln(sprintf('<info>%s</info>', $user->email));
        }

        return Command::SUCCESS;
    }
}
