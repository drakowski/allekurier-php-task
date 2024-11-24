<?php

declare(strict_types=1);

namespace App\Core\User\Application\Command\CreateUser;

use Symfony\Component\Validator\Constraints as Assert;

final class CreateUserCommand
{
    public function __construct(
        #[Assert\Email(
            message: 'Podany adress email {{ value }} nie jest poprawny.',
        )]
        public readonly string $email,
    ) {
    }
}
