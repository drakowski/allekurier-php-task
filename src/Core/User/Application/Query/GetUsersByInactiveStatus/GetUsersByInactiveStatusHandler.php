<?php

namespace App\Core\User\Application\Query\GetUsersByInactiveStatus;

use App\Core\User\Application\DTO\UserEmailDTO;
use App\Core\User\Domain\Repository\UserRepositoryInterface;
use App\Core\User\Domain\Status\UserStatus;
use App\Core\User\Domain\User;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetUsersByInactiveStatusHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {
    }

    public function __invoke(GetUsersByInactiveStatusQuery $query): array
    {
        $users = $this->userRepository->findAllByStatus(UserStatus::INACTIVE);

        return array_map(function (User $user) {
            return new UserEmailDTO(
                $user->getEmail()
            );
        }, $users);
    }
}
